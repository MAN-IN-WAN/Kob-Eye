import React,{useState} from 'react';
import ReactDOM from 'react-dom';
import { useSelector } from 'react-redux';
import Loader from "react-loader-spinner";

export default function CompoHoraires() {

    let horaire = useSelector( (state) => {
        return state.home.horaireData.data;
    });

    if (!horaire){
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
    console.log(horaire,"horaire")

    return (
        <div className={"container"}>
            <div className="row">
                <div className="col-lg-6 col-md-6 col-sm-6" >
                    <div className={"type3 textBlocHoraire type4Smart"}>Horaires</div>
                    <div className={"type1 type5Smart"} dangerouslySetInnerHTML={{
                        __html: horaire.horaires
                    }}></div>
                </div>
                <div className="col-lg-6 col-md-6 col-sm-6" >
                    <div className={"type3 textBlocHoraire type4Smart"}>Adresse</div>
                    <div className={"type1 type5Smart"} dangerouslySetInnerHTML={{
                        __html: horaire.adresse
                    }}></div>
                </div>

            </div>
        </div>
    );
}
