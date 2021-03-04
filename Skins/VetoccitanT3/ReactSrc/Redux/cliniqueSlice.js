import { createSlice } from '@reduxjs/toolkit';
import React from 'react';
import ReactDOM from 'react-dom';

export const cliniqueSlice = createSlice({
    name: 'clinique',
    initialState: {
        fullData:{}
    },
    reducers: {
        load: (state,action) => {
            state.fullData = action.payload.data;
        }
    }

});


const fetchDataClinique = (dispatch) => {
    fetch("/Vetoccitan/Adherent/getConfig.json?confType=clinique")
        .then(res => res.json())
        .then(
            (result) => {
                dispatch(load(
                    {
                        data:result.data
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
export const loadDataClinique = type => dispatch => {
    fetchDataClinique(dispatch);
}


export function ficheHtml(data) {
    let image = data.Image;
    let titre = data.Titre_image;
    let titreArticle = data.Titre;
    if (data.Media && data.Media.Image) image = data.Media.Image;
    if (data.Media && data.Media.Titre) titre = data.Media.Titre;
    let description = "";
    if (data.Description) {
        description = data.Description.replace(/(\.jpe*g)|(\.png)/ig, function (match) {
            let ext = 'jpg';
            if (match.toLowerCase().indexOf('png') != -1)
                ext = 'png';
            return match + '.limit.1600x500.' + ext;
        });
    }

    let text = [];
    text.push(<div className="col-md-12">

        <div className="imgArticle">
            <img src={"/" + image} className="img-fluid"
                 alt={titre} title={titre}/>
        </div>
        <div className="titreArticle">
            <p>{titreArticle}</p>
        </div>
        <div className="textArticle">
            <div dangerouslySetInnerHTML={{
                __html: description
            }}>
            </div>
        </div>

    </div>);
    return text;
}

export const { load } = cliniqueSlice.actions

export default cliniqueSlice.reducer