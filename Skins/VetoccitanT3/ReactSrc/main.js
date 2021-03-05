import React, {useState} from 'react';
import ReactDOM from 'react-dom';
import { Provider, useStore } from 'react-redux';
import { configureStore } from '@reduxjs/toolkit'
import {
    BrowserRouter as Router,
    Switch,
    Route,
    Link,
    NavLink,
    useHistory,
    useParams,
    useRouteMatch,
    useLocation
} from "react-router-dom";
import Page_home from './Pages/Home/Home.js';
import Page_equipe from './Pages/Equipe/Equipe.js';
import Page_clinique from './Pages/Clinique/Clinique.js';
import Page_nsc from './Pages/NSC/Nsc.js';
import CompoNscFiche from "/Pages/NSC/component_nsc_fiche.js";
import Page_contact from "/Pages/Contact/Contact.js";
import Page_ml from "/Pages/Mentions/Mentions.js";
import { GoogleReCaptchaProvider,useGoogleReCaptcha } from 'react-google-recaptcha-v3';
import Navigation from "react-sticky-nav";
import SvgPhone from './Pages/Home/PHONE.svg';
import ScrollToTop from './ScrollToTop.js';

// Redux Thunk
import { default as nscReducer, loadData } from './Redux/nscSlice.js';
import { default as equipeReducer, loadDataTeam } from './Redux/equipeSlice.js';
import { default as cliniqueReducer, loadDataClinique } from './Redux/cliniqueSlice.js';
import { default as homeReducer, loadDataHomeBloc, loadDataHomeSlider, loadDataHomeHoraire } from './Redux/homeSlice.js';

const store = configureStore({
    reducer: {
        nsc: nscReducer,
        equipe: equipeReducer,
        clinique: cliniqueReducer,
        home: homeReducer
    }
})

const components = {
    home:Page_home,
    equipe:Page_equipe,
    clinique:Page_clinique,
    news:Page_nsc,
    services:Page_nsc,
    conseils:Page_nsc,
    contact:Page_contact,
    mentionsLegales:Page_ml
}

function App(){

    return (
        <GoogleReCaptchaProvider
            reCaptchaKey="6LcivUYaAAAAAHDRReHhcQJ54dISZiVqQ25EPaWe"
            scriptProps={{
                async: false, // optional, default to false,
                defer: false, // optional, default to false
                appendTo: "head", // optional, default to "head", can be "head" or "body",
                nonce: undefined // optional, default undefined
            }}
        >
            <Provider store={store}>
                <Router>
                    <ScrollToTop />
                    <Navigation>
                        {position => (
                            <>
                                <div className="menuBar">
                                    <BurgerMenu/>
                                    <div class="container">
                                        <div className={"row infosTop d-lg-flex d-none "+ (position === "unfixed"?'':'hidden')}>
                                            <div className={"col-lg-12 col-md-12"}>
                                                <div className="row justify-content-between">
                                                    <h1 className="col-md-4 infoNom type1">{adherentData.Nom}</h1>
                                                    <div className="col-md-4 infoPhone type2"><span>{adherentData.TelUrgence?"Téléphone d'urgence : "+adherentData.TelUrgence:"Téléphone : "+adherentData.Tel} </span><SvgPhone/></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div className="container">
                                        <div className="row menuContainer ">
                                            <div className={"col-lg-4 col-md-4 col-sm-4 menuNom type2 " + (position === "unfixed"?'hidden':'')}>
                                                {adherentData.Nom}
                                            </div>
                                            <div className="col-lg-8 col-md-8 col-sm-8 menuPrincipal">
                                                <div className="menuTop d-none d-md-block"><Menu/></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </>
                        )}
                    </Navigation>
                    <Main/>
                    <div id="Footer">
                        <Footer/>
                    </div>
                </Router>
            </Provider>
        </GoogleReCaptchaProvider>
    )
}
function Menu(){
    let ourStore = useStore();
    if (Object.keys(ourStore.getState().nsc.fullData).length === 0)
        ourStore.dispatch(loadData("all"));

    if (Object.keys(ourStore.getState().equipe.fullData).length === 0)
        ourStore.dispatch(loadDataTeam("all"));

    if (Object.keys(ourStore.getState().clinique.fullData).length === 0)
        ourStore.dispatch(loadDataClinique());

    if (Object.keys(ourStore.getState().home.blocData).length === 0)
        ourStore.dispatch(loadDataHomeBloc());

    if (Object.keys(ourStore.getState().home.sliderData).length === 0)
        ourStore.dispatch(loadDataHomeSlider());

    if (Object.keys(ourStore.getState().home.horaireData).length === 0)
        ourStore.dispatch(loadDataHomeHoraire());

    var toto = "toto";
    if (window.history.state != null){
        toto = window.history.state.key;
    }


    const test = () => {
        var tata = "tata";
        if (window.history.state != null){
            tata = window.history.state.key;
        }
        if (tata != toto){
            document.getElementById("close").style['max-height'] = "0px";
            document.getElementById("close").style['padding'] = "0px";
        }
    }

    const [Menus,setMenus] = useState(MenusData);
    const generateMenu = () => {
        var menuItems = [];
        for (let i in Menus ){
            let m = Menus[i];
            let exact = {};
            if (m.url == '')  exact ={'exact':''}; // utilise un objet pour attribut NavLink
            menuItems.push(
                <li className="menuItem">
                    <NavLink className="menuLink type2 type3Smart" onClick={test} {...exact} activeClassName="current" to={{pathname:"/"+m.url,state:{titre:m.titre}}}>{m.titre}</NavLink>
                </li>
            )
        }
        // console.log(MenusData,'menusData')
        return menuItems;
    }
    const menuConstruct = () => {
        var menuJSX = generateMenu();
        return (
                <ul className="mainMenu">
                    {menuJSX}
                </ul>
        );
    }

    return (
        menuConstruct()
    );
}

function BurgerMenu(){

    const [MenusMobile,setMenusMobile] = useState(MenusData);
    const [FooterMobile,setFooterMobile] = useState(FooterData);


    const generateRouteMobile = () => {
        var menuItems = [];
        for (let i in MenusMobile) {
            let m = MenusMobile[i];
            if (m.url.length > 0) {
                menuItems.push(
                    <Route exact path={"/" + m.url }>
                        {m.titre}
                    </Route>
                );
                menuItems.push(
                    <Route path={"/" + m.url + '/:url'}>
                        {m.titre}
                    </Route>
                )
            } else {
                menuItems.push(
                    <Route exact path={"/"}>
                        Accueil
                    </Route>
                );

            }
        }
        return menuItems;
    }
    const generateRouteFooterMobile = () => {
        let menuItems = [];
        for (let i in FooterMobile) {
            let m = FooterMobile[i];
            if(m.url.length > 0){
                menuItems.push(
                    <Route exact path={"/"+m.url }>
                        {m.titre}
                    </Route>
                )
            }

        }
        return menuItems;
    }

    const routeConstructMobile = () => {
        // console.log(Menus);
        let routeJSX = generateRouteMobile();
        let routeFooterJSX = generateRouteFooterMobile();
        return (

            <Switch>
                {routeJSX}
                {routeFooterJSX}
            </Switch>
        );
    }

    const initBurger = () => {
        if (document.getElementById("close") != null){
            document.getElementById("close").style['max-height'] = "800px";
            document.getElementById("close").style['padding'] = "15px";
        }
    }

    const testu = () => {
        document.getElementById("close").style['max-height'] = "0px";
        document.getElementById("close").style['padding'] = "0px";
    }


    return (
        <div className="wrapWrap d-lg-none">
            <div id="wrap">
                <div id="open">
                    <h1 className="toChange type1Smart">
                        {routeConstructMobile()}
                    </h1>
                    <a href="#wrap" id="openLink">
                        <img onClick={initBurger} src="/Skins/VetoccitanT3/Images/burger.png"/>
                    </a>
                </div>
                <div id="close">
                    <Menu/>
                    <a href="#" id="closeLink" >
                        <img onClick={testu} src="/Skins/VetoccitanT3/Images/croix.png"/>
                    </a>
                </div>
            </div>
        </div>
    )
}
function Main(){

    const [Menus,setMenus] = useState(MenusData);
    const [Footer,setFooter] = useState(FooterData);
    const generateRoute = () => {
        var menuItems = [];
        for (let i in Menus) {
            let m = Menus[i];
            if(m.url.length > 0){
                let compo = React.createElement(components[m.url], {});
                menuItems.push(
                    <Route exact path={"/"+m.url}>
                        {compo}
                    </Route>
                );

                menuItems.push(
                    <Route path={"/"+m.url+'/:url'}>
                        <CompoNscFiche/>
                    </Route>
                );
            }else{
                menuItems.push(
                    <Route exact path="/">
                        <Page_home/>
                    </Route>
                );


            }

        }

        return menuItems;
    }
    const generateRouteFooter = () => {
        let menuItems = [];
        for (let i in Footer) {
            let m = Footer[i];
            if(m.url.length > 0){
                let compo = React.createElement(components[m.url], {});
                menuItems.push(
                    <Route exact path={"/"+m.url}>
                        {compo}
                    </Route>
                )
            }

        }
        return menuItems;
    }
    const routeConstruct = () => {
        // console.log(Menus);
        let routeJSX = generateRoute();
        let routeFooterJSX = generateRouteFooter();
        return (

            <Switch>
                {routeJSX}
                {routeFooterJSX}
            </Switch>
        );
    }
    return (
        routeConstruct()
    );
}

function Footer(){
    const [Access,setAccess] = useState(AccessData);
    const [Footer,setFooter] = useState(FooterData);
    const accessConstruct = () => {
        let AccessItems = [];
        for (let i in AccessData.Service ){
            let m = AccessData.Service[i];
            AccessItems.push(
                <li>
                    <img className="logoFooter img-fluid" src={"/"+m.logo} alt={m.titre} title={m.titre}/>
                </li>
            )
        }
        for (let i in AccessData.Langue ){
            let m = AccessData.Langue[i];
            AccessItems.push(
                <li>
                    <img className="logoFooter img-fluid" src={"/"+m.logo} alt={m.titre} title={m.titre}/>
                </li>
            )
        }
        return (
            <ul id={"access"}>{AccessItems}</ul>
        );
    }
    const generateMenuFooter = () => {
        let FooterData = Footer;
        let menuItems = [];
        for (let i in FooterData ){
            let m = FooterData[i];
            menuItems.push(
                <li>
                    <NavLink className="menuLink type2" to={{pathname:"/"+m.url,state:{titre:m.titre}}}>{m.titre}</NavLink>
                </li>
            )
        }
        if (adherentData.LienFacebook != ""){
            menuItems.push(
                <li>
                    <a class="menuLink type2" href={adherentData.LienFacebook}>Facebook</a>
                </li>
            );
        }
        if (adherentData.LienInstagram != "") {
            menuItems.push(
                <li>
                    <a class="menuLink type2" href={adherentData.LienInstagram}>Instagram</a>
                </li>
            )
        }
        return menuItems;
    }
    const routeConstruct = () => {
        var menuFooterJSX = generateMenuFooter();

        return (
            <ul id={"menuFooter"}>
                {menuFooterJSX}
            </ul>
        );
    }
    const go = () => {
        let menuFooter = routeConstruct();
        let access = accessConstruct();
        return [access,menuFooter];
    }
    return (
        go()
    );
}



ReactDOM.render(
    <App/>,
    document.getElementById('App')
);


