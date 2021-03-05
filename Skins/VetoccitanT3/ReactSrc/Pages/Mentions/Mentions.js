import React,{useState} from 'react';
import ReactDOM from 'react-dom';



export default function Page_ml(){


    return(
        <>
            <div dangerouslySetInnerHTML={{
                __html:  mentionsData.Gauche
            }}></div>
            <div dangerouslySetInnerHTML={{
                __html:  mentionsData.Centre
            }}></div>
            <div dangerouslySetInnerHTML={{
                __html:  mentionsData.Droite
            }}></div>
        </>
    )
}
