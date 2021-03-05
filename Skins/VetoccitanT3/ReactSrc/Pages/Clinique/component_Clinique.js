import React , { useState } from 'react';
import ReactDOM from 'react-dom';
import { useLocation,useParams } from "react-router-dom";
import { useSelector } from 'react-redux';
import { ficheHtml } from '../../Redux/cliniqueSlice';

export default function CompoClinique(){

    let selector = useSelector( (state) => {
        return state.clinique.fullData;
    });
let  text = ficheHtml(selector);
    return (
        <>
            {text}
        </>
    )

}

