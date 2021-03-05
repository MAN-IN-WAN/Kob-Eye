import { createSlice } from '@reduxjs/toolkit';
import React from 'react';
import ReactDOM from 'react-dom';

export const homeSlice = createSlice({
    name: 'home',
    initialState: {
        blocData: {},
        sliderData: {},
        horaireData: {}

    },
    reducers: {
        loadBloc: (state,action) => {
            state.blocData = {'data':action.payload.data};
        },
        loadSlider: (state,action) => {
            state.sliderData = {'data':action.payload.data};
        },
        loadHoraire: (state,action) => {
            state.horaireData = {'data':action.payload.data};
        }
    }

});


const fetchDataHomeBloc = (dispatch) => {
    fetch("/Vetoccitan/Adherent/getConfig.json?confType=homeBloc")
        .then(res => res.json())
        .then(
            (result) => {
                dispatch(loadBloc(
                    {
                        data:result.data
                    }
                ));
            },
            // Remarque : il est important de traiter les erreurs ici
            // au lieu d'utiliser un bloc catch(), pour ne pas passer à la trappe
            // des exceptions provenant de réels bugs du composant.
            (error) => {
                // this.setState({
                //     isLoaded: true,
                //     error
                // });
            }
        );
}

export const loadDataHomeBloc = () => dispatch => {

    fetchDataHomeBloc(dispatch);

}

const fetchDataHomeSlider = (dispatch) => {
    fetch("/Vetoccitan/Adherent/getConfig.json?confType=homeSlider&POSITION=bandoHaut")
        .then(res => res.json())
        .then(
            (result) => {
                dispatch(loadSlider(
                    {
                        data:result.data
                    }
                ));
            },
            // Remarque : il est important de traiter les erreurs ici
            // au lieu d'utiliser un bloc catch(), pour ne pas passer à la trappe
            // des exceptions provenant de réels bugs du composant.
            (error) => {
                // this.setState({
                //     isLoaded: true,
                //     error
                // });
            }
        );
}

export const loadDataHomeSlider = () => dispatch => {

    fetchDataHomeSlider(dispatch);

}

const fetchDataHomeHoraire = (dispatch) => {
    fetch("/Vetoccitan/Adherent/getConfig.json?confType=homeHoraires")
        .then(res => res.json())
        .then(
            (result) => {
                dispatch(loadHoraire(
                    {
                        data:result.data
                    }
                ));
            },
            // Remarque : il est important de traiter les erreurs ici
            // au lieu d'utiliser un bloc catch(), pour ne pas passer à la trappe
            // des exceptions provenant de réels bugs du composant.
            (error) => {
                // this.setState({
                //     isLoaded: true,
                //     error
                // });
            }
        );
}

export const loadDataHomeHoraire = () => dispatch => {

    fetchDataHomeHoraire(dispatch);

}



export const { loadBloc, loadSlider, loadHoraire } = homeSlice.actions

export default homeSlice.reducer