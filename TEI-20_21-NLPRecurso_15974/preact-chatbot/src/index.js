import './style.css';
import { Component } from 'preact';
import { useEffect, useRef } from 'react';
import Pusher from 'pusher-js';
import Header from './components/Header';
import Footer from './components/Footer';
import 'font-awesome/css/font-awesome.min.css';

export default class App extends Component {
    pusher = new Pusher('c6fd4a5bffe766af91b3', {
        cluster: 'eu',
        useTLS: true,
    });

    botResponded = false;
    userSubmited = false;

    constructor(props) {
        super(props);
        this.state = {
            userMessage: '',
            conversation: [],
            chatrooms: [],
            roomID: 1,
        };

        fetch('http://localhost:9000/history').then(res => {
            return res.json();
        }).then(data => {
            const filteredConvo = data.filter((convers) => convers.roomId === this.state.roomID);
            this.setState({
                conversation: filteredConvo
            })
        });

        fetch('http://localhost:9000/rooms').then(res => {
            return res.json();
        }).then(data => {
            this.setState({
                chatrooms: data
            })
        });

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
    }

    changeBots(chatRoomID) {
        this.setState({
            roomID: chatRoomID
        });
    }

    updateConvo(chatRoomID) {
        this.changeBots(chatRoomID)
        fetch('http://localhost:9000/history').then(res => {
            return res.json();
        }).then(data => {
            const filteredConvo = data.filter((convers) => convers.roomId === this.state.roomID);
            this.setState({
                conversation: filteredConvo
            })
        });
    }

    componentDidUpdate(prevProps, prevState) {
        if ((prevState.roomID !== this.state.roomID || prevState.chatrooms !== this.state.chatrooms || prevState.conversation !== this.state.conversation) && this.botResponded === false && this.userSubmited === true) {
            const channel = this.pusher.subscribe('bot' + this.state.roomID);
            channel.unbind_all;
            channel.bind('bot-response', data => {
                var findID = 0;
                fetch('http://localhost:9000/history').then(res => {
                    return res.json();
                }).then(data => {
                    findID = data.length + 1;
                });
                const msg = {
                    id: findID,
                    text: data.message,
                    user: 'ai',
                    roomId: this.state.roomID,
                };
                this.setState({
                    conversation: [...this.state.conversation, msg],
                });
                fetch('http://localhost:9000/history', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(msg)
                });
            });
            this.botResponded = true;
            this.userSubmited = false;
        }
    }

    handleChange(event) {
        this.setState({ userMessage: event.target.value });
    }


    handleDelete(passedId, ind) {
        console.log(this.state.conversation)
        const conversationToFilter = this.state.conversation;

        const filteredConvo = conversationToFilter.filter((convers, index) => index !== ind);
        this.setState({
            conversation: filteredConvo
        });
        fetch('http://localhost:9000/history/' + passedId, {
            method: 'DELETE'
        })
    }

    handleDeleteBot(chatRoomID, buttonID) {
        const botsToFilter = this.state.chatrooms;
        const filteredBots = botsToFilter.filter((bot, index) => index !== buttonID);
        fetch('http://localhost:9000/rooms/' + chatRoomID, {
            method: 'DELETE'
        })
        const passingID = buttonID;

        this.setState({
            chatrooms: filteredBots,
        });
        this.changeBots(passingID);

        fetch('http://localhost:9000/history').then(res => {
            return res.json();
        }).then(data => {
            const filteredConvo = data.filter((convers) => convers.roomId !== this.state.roomID);
            this.setState({
                conversation: filteredConvo
            })
        });
    }

    handleSubmit(event) {
        event.preventDefault();
        this.botResponded = false;
        var findID = 0;
        fetch('http://localhost:9000/history').then(res => {
            return res.json();
        }).then(data => {
            findID = data.length + 1;
        });
        const msg = {
            id: findID,
            text: this.state.userMessage,
            user: 'user',
            roomId: this.state.roomID,
        };

        fetch('http://localhost:9000/history', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(msg)
        });

        this.setState({
            conversation: [...this.state.conversation, msg],
        });
        this.userSubmited = true;

        fetch('http://localhost:7777/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: this.state.userMessage,
                chatRoomID: this.state.roomID,
            }),
        });


        this.setState({ userMessage: '' });

    }

    handleAddBot() {
        if (this.state.chatrooms.length < 5) {
            const lastDigit = this.state.chatrooms.length - 1
            const msg = {
                id: this.state.chatrooms[lastDigit].id + 1,
            };

            fetch('http://localhost:9000/rooms', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(msg)
            });

            this.setState({
                chatrooms: [...this.state.chatrooms, msg],
            });

            const idChange = this.state.chatrooms.length + 1;
            this.changeBots(idChange);
            fetch('http://localhost:9000/history').then(res => {
                return res.json();
            }).then(data => {
                const filteredConvo = data.filter((convers) => convers.roomId === this.state.roomID);
                this.setState({
                    conversation: filteredConvo
                })
            });
        }
    }


    render() {
        const AlwaysScrollToBottom = () => {
            const elementRef = useRef();
            useEffect(() => elementRef.current.scrollIntoView());
            return <div ref={elementRef} />;
        };

        const ChatBubble = (text, i, className, ind) => {
            const classes = `${className} chat-bubble`;
            return (
                <div key={`${className}-${ind}`} class={`${className} chat-bubble`}>
                    <span class="chat-content">
                        {text}
                        <button className="deleteBot" onClick={() => { this.handleDelete(i, ind) }}>
                            <i className="fa fa-trash fa-lg" aria-hidden="true" />
                        </button>
                        <p style="font-size:50%">{className}</p>
                    </span>
                </div>
            );
        };

        const ChatButton = (chatRoomID, buttonID) => {
            return (
                <button className="btn" onClick={() => { this.updateConvo(chatRoomID) }}>Chatroom {chatRoomID}
                    <button className="deleteBot" onClick={() => { this.handleDeleteBot(chatRoomID, buttonID) }}>
                        <i className="fa fa-trash fa-2x" aria-hidden="true" />
                    </button>
                </button>
            )
        }

        const chat = this.state.conversation.map((e, index) =>
            ChatBubble(e.text, e.id, e.user, index)
        );

        const chatrooms = this.state.chatrooms.map((e, index) =>
            ChatButton(e.id, index)
        )

        return (
            <div>
                <Header />
                <div>
                    {chatrooms}
                    <button className="btn" onClick={() => { this.handleAddBot() }}>+</button>
                </div>
                <div class="chat-window">
                    <div class="conversation-view">
                        {chat}
                        {AlwaysScrollToBottom()}
                    </div>
                    <div class="message-box">
                        <form className="form" onSubmit={this.handleSubmit}>
                            <input
                                value={this.state.userMessage}
                                onInput={this.handleChange}
                                class="text-input"
                                type="text"
                                autofocus
                                placeholder="Type your message and hit Enter to send"
                            />
                        </form>
                    </div>
                </div>
                <Footer />
            </div>
        );
    }
}