import React,{useState} from 'react';
import ReactDOM from 'react-dom';
// import AwesomeSlider from 'react-awesome-slider';
import 'react-awesome-slider/dist/styles.css';
import AutoplaySlider from 'react-awesome-slider/src/hoc/autoplay';
import { useSelector } from 'react-redux';


export default function CompoSlider(){

    let slider = useSelector( (state) => {
        return state.home.sliderData.data;
    });

    let slides = [];
    let texte = "";

    for (let i in slider){
        let anim = "";
        if (slider[i].Animation === "gauche"){
            anim = "animGauche";
        }else{
            anim = "animDroite";
        }
        slides.push(
            <div data-src={ slider[i].Media.Image} className={"itemSlide "+anim}>
                <div className="messageSlide" dangerouslySetInnerHTML={{
                    __html: slider[i].Texte
                }}></div>
            </div>
        );
        slides.push(
            <div data-src={ slider[i].Media.Image} className={"itemSlide "+anim}>
                <div className="messageSlide" dangerouslySetInnerHTML={{
                    __html: slider[i].Texte
                }}></div>
            </div>
        )
    };
    texte = <AutoplaySlider
        play={true}
        cancelOnInteraction={false} // should stop playing on user interaction
        interval={5000}
        bullets={false}
        buttons={false}
    >
        {slides}
    </AutoplaySlider>;

    return (
        <>{texte}</>
    );

}
