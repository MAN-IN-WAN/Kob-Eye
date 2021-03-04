import { createSlice } from '@reduxjs/toolkit';
import React from 'react';
import ReactDOM from 'react-dom';

export const equipeSlice = createSlice({
    name: 'equipe',
    initialState: {
        fullData:{}
    },
    reducers: {
        load: (state,action) => {
            state.fullData[action.payload.type] = action.payload.data;
        }
    }

});


const fetchDataTeam = (dispatch,type) => {
    var donnees = [];
    fetch("/Vetoccitan/Adherent/getConfig.json?confType="+type)
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
                // console.log(texte);

            },
            (error) => {
                // this.setState({
                //     isLoaded: true,
                //     error
                // });
            }
        );
}

export const loadDataTeam = type => dispatch => {
    let finalText = [];
    if( type == "all"){
        fetchDataTeam(dispatch,"fullEquipe");
        fetchDataTeam(dispatch,"homeEquipe");
    }else{
        fetchDataTeam(dispatch,type);
    }

}



export const { load } = equipeSlice.actions

export default equipeSlice.reducer