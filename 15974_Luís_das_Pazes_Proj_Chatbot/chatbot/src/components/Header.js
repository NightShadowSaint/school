import imgsrc from '../Images/astronaut.png';

const Header = () => {
    return (
        <header className="header">
            <h1 className="title">Space Buddy </h1>
            <img src={imgsrc} alt="logo" width={60} />
        </header>
    )
}

export default Header
