import React,{ useEffect } from 'react';
import ReactDOM from 'react-dom';
import { useSelector, useDispatch } from 'react-redux';
import { useLocation } from 'react-router-dom';
import { incrementPage, decrementPage, changePage } from '../../Redux/nscSlice.js'

export default function CompoPagination(){

    let location = useLocation();
    let dispatch = useDispatch();
    let type = location.pathname.substring(1);

    let selectorData = useSelector( (state) => {
        return state.nsc.filterData[type];
    });
    let selectorPage = useSelector( (state) => {
        return state.nsc.page[type];
    });

    let pageCount = 1;
    if (selectorData){
        let count = !!selectorData?selectorData.length:0;
        pageCount = Math.ceil(count/paginationData.itemsCount);
    }

    let texte = [];

    if (pageCount > 1){
        let sel = !!parseInt(selectorPage)?parseInt(selectorPage):1;
        let bclass = sel>1?"active":"inactive";
        let aclass = sel<pageCount?"active":"inactive";
        let fclass = sel!=1?"active":"inactive";
        let lclass = sel!=pageCount?"active":"inactive";

        texte.push(<>
            <li className={bclass} onClick={
                () =>
                    dispatch(
                        decrementPage(
                            {
                                type:type
                            }
                        )
                    )
            }>
                <span className={"prec"}>PREV</span>
            </li>
            <li className={fclass} onClick={
                () =>
                    dispatch(
                        changePage(
                            {
                                type:type,
                                value:1
                            }
                        )
                    )
            }>
                1
            </li>
            </>
        )
        if (sel>=3){
            texte.push(
                <li>...</li>
            )
        }

        if (sel != 1 && sel != pageCount){
            texte.push(
                <li>
                    {sel}
                </li>
            )
        }

        if (sel<=(pageCount-2)){
            texte.push(
                <li>...</li>
            )
        }
        texte.push(
            <>
            <li className={lclass} onClick={
                () =>
                    dispatch(
                        changePage(
                            {
                                type:type,
                                value:pageCount
                            }
                        )
                    )
            }>
                {pageCount}
            </li>
            <li className={aclass} onClick={
                () =>
                    dispatch(
                        incrementPage(
                            {
                                type:type
                            }
                        )
                    )
            }>
                <span className={"suiv"}>NEXT</span>
            </li>
            </>
        )
    }

    return (
        <>
            <div id={"pagination"} className={"row"}>
                <ul className={"col-md-12"}>{texte}</ul>
            </div>
        </>
    );
}
