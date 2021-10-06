import React, { useState, useEffect } from 'react'
import MessageList from './MessageList.js';

export default function Chitchat() {
    const [chatTxts, setChatTexts] = useState(null);
    const [recQuestions, setRecQuest] = useState(null);
    const [recAnswers, setRecAns] = useState(null);

    useEffect(() => {
        fetch('http://localhost:9000/history').then(res => {
            return res.json();
        }).then(data => {
            setChatTexts(data);
        })
        fetch('http://localhost:9000/questions').then(res => {
            return res.json();
        }).then(data => {
            setRecQuest(data);
        })
        fetch('http://localhost:9000/answers').then(res => {
            return res.json();
        }).then(data => {
            setRecAns(data);
        })
    }, []);

    const handleDelete = (id) => {
        const newTxts = chatTxts.filter(chatTxt => chatTxt.id !== id);
        setChatTexts(newTxts);
        fetch('http://localhost:9000/history/' + id, {
            method: 'DELETE'
        })
    }

    const handleSubmit = (e) => {
        e.preventDefault();
        const posttext = { tmssg, typer: "User" }
        var askedID = 0;
        var flag = false;

        fetch('http://localhost:9000/history', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(posttext)
        });
        settmssg("");
        fetch('http://localhost:9000/history').then(res => {
            return res.json();
        }).then(data => {
            setChatTexts(data);
        });
        fetch('http://localhost:9000/questions').then(res => {
            return res.json();
        }).then(data => {
            setRecQuest(data);
        })
        fetch('http://localhost:9000/answers').then(res => {
            return res.json();
        }).then(data => {
            setRecAns(data);
        })

        for (let i = 0; i < recQuestions.length; i++) {
            const askedQ = recQuestions[i].question;
            if (tmssg.toLowerCase().includes(askedQ)) {
                askedID = recQuestions[i].id;
                break
            } else if((i + 1) == recQuestions.length) {
                flag = true;
            }
        };
        if (flag === true) {
            const dunno = { tmssg: "Sorry, I don't know that yet.", typer: "Bot" };
            fetch('http://localhost:9000/history', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dunno)
            });
        }

        for (let i = 0; i < recAnswers.length; i++) {
            const ansID = recAnswers[i].id;
            if (ansID === askedID) {
                tmssg = recAnswers[i].answer;
                const postAns = { tmssg, typer: "Bot" }
                fetch('http://localhost:9000/history', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(postAns)
                });
            }
        }
        fetch('http://localhost:9000/history').then(res => {
            return res.json();
        }).then(data => {
            setChatTexts(data);
        });
        fetch('http://localhost:9000/history').then(res => {
            return res.json();
        }).then(data => {
            setChatTexts(data);
        });
    };

    var [tmssg, settmssg] = useState('');

    return (
        <div>
            <div className="container">
                <div className="chatbot-card">
                    {chatTxts && <MessageList chatTxts={chatTxts} handleDelete={handleDelete} />}
                </div>
            </div>
            <div className="container2">
                <form className="chitchat" onSubmit={handleSubmit}>
                    <input className="human-input"
                        required
                        type="text"
                        placeholder="Ask me something"
                        value={tmssg}
                        onChange={(e) => settmssg(e.target.value)}
                    />
                    <button className="btn" type="submit" value="submit">Send</button>
                </form>
            </div>
        </div>
    )
}