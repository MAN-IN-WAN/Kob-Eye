import React,{useState} from 'react';
import SvgUrgence from './URGENCE.svg';
import SvgTeam from './team.svg';
import SvgVeterinary from './veterinary.svg';
import SvgShop from './shop.svg';
import { useSelector } from 'react-redux';


export default function CompoBlocAccueil (){

    let selector = useSelector( (state) => {
        return state.home.blocData.data;
    });

    let eshop =
        <>
            <SvgTeam/>
            <h3 class="titreBlocAccueil type1 type2Smart" >L'Equipe</h3>
            <div class="bodyBlocAccueil type5"><a href="/equipe">En savoir +</a></div>
        </>
    ;
    if (selector != null && selector.lienEshop != null){
        eshop = <>
                <SvgShop/>
                <h3 class="titreBlocAccueil type1 type2Smart">L'Eshop</h3>
                <div class="bodyBlocAccueil type5"><a href={ adherent.lienEshop }>En savoir +</a></div>
            </>
        ;
    }

    return (
        <div id="bloc">
            <div className="row blocAccueil">
                <div className="col-md-4 iconeAccueil" >
                    <SvgVeterinary/>
                    <h3 className="titreBlocAccueil type1 type2Smart">La structure</h3>
                    <div className="bodyBlocAccueil type5"><a href="/clinique">En savoir +</a></div>
                </div>
                <div className="col-md-4 iconeAccueil">
                    {eshop}
                </div>
                <div class="col-md-4 iconeAccueil" >
                    <SvgUrgence/>
                    <h3 class="titreBlocAccueil type1 type2Smart" >Urgence</h3>
                    <div class="bodyBlocAccueil type5"><a href="/urgence">En savoir +</a></div>
                </div>
            </div>
        </div>
    );
}

