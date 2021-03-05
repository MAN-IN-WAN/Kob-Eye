import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import { Link } from "react-router-dom";
import { useSelector } from 'react-redux';
import Loader from "react-loader-spinner";

export default function CompoEquipe(props){
    const [focus,setFocus] = useState();
    const [displayFocus,setDisplayFocus] = useState(true);

    let selector = useSelector( (state) => {
        return state.equipe.fullData[props.type];
    });

    console.log(selector,"selector");

    const formatEquipe = (data) => {
        console.log(data,"data");

        if (!data){
            return (
                    <div className={"row justify-content-center"}>
                        <Loader
                            type="Circles"
                            color={mainColor}
                            height={200}
                            width={200}
                            className={"col-md-4 spinnerColor"}
                        />
                    </div>
                )

        }
        let text = [];
        for(let key2 in data.OrdrePoste) {
            let items = data.OrdrePoste[key2];
            text.push(
                <div className="row postHead text-center">
                    <h3 className={"col-md-12 type4 type2Smart"}>{items.Nom}</h3>
                </div>
            );
            let content = [];
            for(let key3 in data.Poste){
                let poste = data.Poste[key3];
                // console.log(poste);
                if (key3 == items.Nom){
                    for ( let key4 in poste){
                        let values = poste[key4];

                        let img = null;
                        if (values.Photo != undefined && values.Photo != null && values.Photo != "") {
                            img =<img src={values.Photo + ".limit.200x200.jpg"}
                                      className="img-responsive imgequipe" alt={ values.Nom }
                                      title={values.Nom}/>;

                        }
                        if (props.type == "fullEquipe"){
                            content.push(
                                <>
                                    <div onClick={(event)=>{clickPerso(event,values.Id)}} className="col-lg-4 col-md-4 col-sm-12 col-xs-12 itemPerso">
                                        {img}
                                        <hr/>
                                        <div className={"type1 type1Smart"}>{values.Nom} {values.Prenom}</div>
                                        <div className={"couleurPrincipal"}>{values.Poste}</div>
                                        <div className={"voirplus"}>
                                            En savoir +
                                        </div>
                                    </div>

                                </>
                            );
                        }else{
                            content.push(
                                <>
                                    <div onClick={(event)=>{clickPerso(event,values.Id)}} className="col-lg-4 col-md-4 col-sm-12 col-xs-12 itemPerso">
                                        {img}
                                        <div className={"type1"}>{values.Nom} {values.Prenom}</div>
                                        <div className={"couleurPrincipal"}>{values.Poste}</div>
                                    </div>

                                </>
                            );
                        }

                    }

                }

            }
            // console.log(text);
            text.push(
                <div className={"row justify-content-around separatePost"}>
                    {content}
                </div>
            )
        }
        return text;
    }
    let texte = formatEquipe(selector);


    const clickPerso = (event,id) => {
        if (props.type == "fullEquipe"){
            for (let p in selector.Poste){
                let people = selector.Poste[p];
                for (let g in people){
                    if (people[g].Id == id){
                        setFocus(people[g]);
                        setDisplayFocus(true);
                    }
                }
            }
        }
        return false;
    }

    let texteFocus = !!focus ? <div id="animBot" className={displayFocus?"active":"inactive"} onClick={() =>{
        setDisplayFocus(false);
        setTimeout(() =>{
            setFocus();
        },300)
    }}>
        <div id="leftSide">
            <img className="imagePers" src={"/"+ (focus.Photo2?focus.Photo2:focus.Photo)+'.limit.2000x2000.jpg' } alt={ focus.Prenom +" "+ focus.Nom }/>
        </div>
        <div id="rightSide">
            <img className="cercle" src="/Skins/VetoccitanT2/Images/croix.png" alt="Fermer"/>
            <div id="detailVeto">
                <div className="docHead"><p className="nomdct"> { focus.Prenom } { focus.Nom }</p>
                    <p className="PersPost">{ focus.Poste }</p>
                </div>
                <div dangerouslySetInnerHTML={{
                    __html:  focus.Description
                }}></div>
            </div>
            <div id="boxContact">
                <Link to={"/contact"} className="contactEquipe">Contactez-nous</Link>
            </div>
        </div>
    </div>:<></>;

    return (
        <>
            {texte}
            {texteFocus}
        </>
    );
}
