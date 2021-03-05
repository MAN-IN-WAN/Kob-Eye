import React,{ useEffect } from 'react';
import ReactDOM from 'react-dom';
import { useSelector, useDispatch } from 'react-redux';
import { useLocation } from 'react-router-dom';
import { changeFilter } from '../../Redux/nscSlice.js'

export default function CompoFilter(){
    let location = useLocation();
    let dispatch = useDispatch();
    let type = location.pathname.substring(1);
    let selector = useSelector( (state) => {
        return state.nsc.filter[type];
    });
    
    let filtres = [];
    for (let filtre in filtreData){
        let classe = "inactive";
        let sel = !!parseInt(selector)?parseInt(selector):0;
        if (parseInt(filtreData[filtre]) === sel)
            classe = "active";
        filtres.push(
            <li className={classe} onClick={() =>
                dispatch(
                    changeFilter(
                        {
                            type:type,
                            value:filtreData[filtre]
                        }
                    )
                )
            }>
                {filtre}
            </li>
        );
    }
    let texte = <ul className={"col-md-12"}>
                    {filtres}
                </ul>;

    return (
        <>
            <div id={"filtresNsc"} className={"row"}>
                {texte}
            </div>
        </>
    );
}
