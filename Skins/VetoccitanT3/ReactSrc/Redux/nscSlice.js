import { createSlice } from '@reduxjs/toolkit';
import React from 'react';
import ReactDOM from 'react-dom';
import HTMLEllipsis from 'react-lines-ellipsis/lib/html';
import { Link } from "react-router-dom";
import Loader from "react-loader-spinner";

export const nscSlice = createSlice({
    name: 'nsc',
    initialState: {
        fullData:{},
        filterData:{},
        pageData: {},
        filter : {},
        page :{}
    },
    reducers: {
        load: (state,action) => {
            state.page[action.payload.type] = 1;
            state.fullData[action.payload.type] = action.payload.data;
            state.filterData[action.payload.type] = displayData(state,action.payload.type);
            state.pageData[action.payload.type] = displayDataPage(state,action.payload.type);
        },
        incrementPage: (state, action) => {
            state.page[action.payload.type] = !!parseInt(state.page[action.payload.type])?parseInt(state.page[action.payload.type])+1:2;
            let count = !!state.filterData[action.payload.type]?state.filterData[action.payload.type].length:0;
            let pageCount = Math.ceil(count/paginationData.itemsCount);
            if (pageCount < state.page[action.payload.type]) state.page[action.payload.type] = pageCount;
            state.pageData[action.payload.type] = displayDataPage(state,action.payload.type);
        },
        decrementPage: (state, action) => {
            state.page[action.payload.type] = !!parseInt(state.page[action.payload.type])?parseInt(state.page[action.payload.type])-1:1;
            if (1 > state.page[action.payload.type]) state.page[action.payload.type] = 1;
            state.pageData[action.payload.type] = displayDataPage(state,action.payload.type);
        },
        changePage: (state, action) => {
            let value = !!parseInt(action.payload.value)?parseInt(action.payload.value):1;
            state.page[action.payload.type] = action.payload.value;
            let count = !!state.filterData[action.payload.type]?state.filterData[action.payload.type].length:0;
            let pageCount = Math.ceil(count/paginationData.itemsCount);
            if (pageCount < state.page[action.payload.type]) state.page[action.payload.type] = pageCount;
            if (1 > state.page[action.payload.type]) state.page[action.payload.type] = 1;
            state.pageData[action.payload.type] = displayDataPage(state,action.payload.type);
        },
        changeFilter: (state, action) => {
            state.page[action.payload.type] = 1;
            let type = action.payload.type;
            state.filter[type] = action.payload.value;

            state.filterData[type] = displayData(state,type);
            state.pageData[type] = displayDataPage(state,type);

        }
    }

});


export const fetchData = (dispatch,type) => {
    var donnees = [];
    fetch("/Vetoccitan/Adherent/getConfig.json?confType=Nsc&Choix="+type)
        .then(res => res.json())
        .then(
            (result) => {
                donnees = result.data;
                // console.log(result.data);
                dispatch(load(
                    {
                        type:type,
                        data:donnees
                    }
                ));
            },
            (error) => {
                // this.setState({
                //     isLoaded: true,
                //     error
                // });
            }
        );
}

export const loadData = type => dispatch => {
    let finalText = [];
    if( type == "all"){
        fetchData(dispatch,"services");
        fetchData(dispatch,"news");
        fetchData(dispatch,"conseils");
    }else{
        fetchData(dispatch,type);
    }

}

export const buildHtml = (data,type) => {
    var texte = [];
    if(data == undefined){
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
    }else if(!data.length){
        return (
            <div>
                Aucun resultat
            </div>
        )
    }else{
        for ( var e in data){
            let element = data[e];
            // console.log(element,"element");
            if (element.Media.Image == undefined  ){
                texte.push(
                    <div className="row nsc">
                        <div className="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                            <img
                                src="/Home/no-image.png"
                                className="img-fluid imgnsc"
                                alt={element.Titre} title={element.Titre}/>
                        </div>
                        <div className="col-lg-7 col-md-7 col-sm-12 col-xs-12 texteList">
                            <div className="titreArticle">
                                <h1>{element.Titre}</h1>
                            </div>
                            <div className="textArticle">
                                <p className="texte">
                                    <HTMLEllipsis
                                        unsafeHTML={element.Description}
                                        maxLine='2'
                                        ellipsis='...'
                                        basedOn='words'
                                    />
                                </p>
                                <Link to={"/" +type+"/"+element.Url} className="voirplus">
                                    Voir plus...
                                </Link>
                            </div>
                        </div>
                    </div>
                )
            }else{
                texte.push(
                    <div className="row nsc">
                        <div className="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                            <img
                                src={element.Media.Image+".limit.500x500.jpg"}
                                className="img-fluid imgnsc"
                                alt={element.Titre} title={element.Titre}/>
                        </div>
                        <div className="col-lg-7 col-md-7 col-sm-12 col-xs-12 texteList">
                            <div className="titreArticle">
                                <h1>{element.Titre}</h1>
                            </div>
                            <div className="textArticle">
                                <p className="texte">
                                    <HTMLEllipsis
                                        unsafeHTML={element.Description}
                                        maxLine='2'
                                        ellipsis='...'
                                        basedOn='words'
                                    />
                                </p>
                                <Link to={"/"+type+"/"+element.Url} className="voirplus">Voir
                                    plus...</Link>
                            </div>
                        </div>
                    </div>
                )
            }

        }
        var finalTexte=
            <div id="nsc">
                {texte}
            </div>
        ;

        return finalTexte;
    }


}

export const displayData = (state,type) =>{
    let data = state.fullData[type];
    let filterData = [];

    if (!parseInt(state.filter[type])){
        filterData = data;
    }else{
        for (let item in data){
            for(let cat in data[item].cats){
                if (parseInt(data[item].cats[cat]) === parseInt(state.filter[type])){
                    filterData.push(data[item]);
                    break;
                }
            }
        }
    }
    return filterData;
}

export const displayDataPage = (state,type) =>{
    let data = state.filterData[type];
    let pageData = [];
    let page = !!parseInt(state.page[type])?parseInt(state.page[type]):1;

    if (data){
        pageData = data.slice((page-1)*paginationData.itemsCount,page*paginationData.itemsCount);
    }

    return pageData;
}



export const { load, incrementPage, decrementPage, changePage, changeFilter } = nscSlice.actions

export default nscSlice.reducer