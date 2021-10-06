import { Row, Col } from 'reactstrap';

const MessageList = ({ chatTxts, handleDelete }) => {
    return (
        <div>
            {chatTxts.map((chatTxt) => (
                <Row className={chatTxt.typer === "User" ? "userTxts" : "botTxts"}>
                    <div className={chatTxt.typer === "User" ? "txt-preview" : "bottxt-preview"} key={chatTxt.id}>
                        <p>{chatTxt.tmssg}</p>
                        <Row className={chatTxt.typer === "User" ? "txtfootuser" : "txtfootbot"}>
                            <Col>
                                {chatTxt.typer === "User" && <button className={chatTxt.typer === "User" ? "delete" : "deleteBot"} onClick={() => handleDelete(chatTxt.id)}>Delete</button>}
                                {chatTxt.typer === "Bot" && <p className={chatTxt.typer === "User" ? "user" : "bot"}>{chatTxt.typer}</p>}
                            </Col>
                            <Col>
                                {chatTxt.typer === "User" && <p className={chatTxt.typer === "User" ? "user" : "bot"}>{chatTxt.typer}</p>}
                                {chatTxt.typer === "Bot" && <button className={chatTxt.typer === "User" ? "delete" : "deleteBot"} onClick={() => handleDelete(chatTxt.id)}>Delete</button>}
                            </Col>
                        </Row>
                    </div>
                </Row>
            ))}
        </div>
    )
}

export default MessageList;