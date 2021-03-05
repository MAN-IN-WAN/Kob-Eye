import React from 'react';
import ReactDOM from 'react-dom';
import CompoNsc from './component_nsc.js';
import CompoFilter from './component_filtres.js';
import CompoPagination from './component_pagination.js';


export default function Page_nsc(){
    return (
        <div className={"container"}>
            <div id="PageNsc">
                <div className="container">
                    <CompoFilter/>
                    <CompoPagination/>
                    <CompoNsc/>
                    <CompoPagination/>
                </div>
            </div>
        </div>
    );

}
