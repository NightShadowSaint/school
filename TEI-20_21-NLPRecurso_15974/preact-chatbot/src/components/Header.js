import imgsrc from '../Images/car.png';

const Header = () => {
    return (
        <header className="header">
            <h1 className="title">Driving Buddy </h1>
            <img src={imgsrc} alt="logo" width={60} />
        </header>
    )
}

export default Header
