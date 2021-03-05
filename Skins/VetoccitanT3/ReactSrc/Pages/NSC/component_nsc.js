import React from 'react';
import ReactDOM from 'react-dom';
import HTMLEllipsis from 'react-lines-ellipsis/lib/html';
import { useSelector } from 'react-redux';
import { useLocation } from "react-router-dom";
import { buildHtml } from '../../Redux/nscSlice.js'

export default function CompoNsc(){
    let location = useLocation();
    let type = location.pathname.substring(1);
    let selector = useSelector( (state) => {
        return state.nsc.pageData[type];
    });
    let texte = buildHtml(selector,type);

    return (
        <>
            {texte}
        </>
    );
}
