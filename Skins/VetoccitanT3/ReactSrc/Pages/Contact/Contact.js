import React,{useState} from 'react';
import ReactDOM from 'react-dom';
import CompoEquipe from '../Home/component_Equipe.js';
import { GoogleReCaptchaProvider,useGoogleReCaptcha } from 'react-google-recaptcha-v3';


export default function Page_contact(){
    const [stateInput,setStateInput] = useState({
        Nom:"",
        Prenom:"",
        Mail:"",
        Tel:"",
        Ville:"",
        Departement:"",
        Message:""
    });

    const [error,setError] = useState({
        status:false,
        message:""
    });

    const [success,setSuccess] = useState({
        status:false,
        message:""
    });

    const [enCours,setEnCours] = useState(false);

    let formClass = enCours?"active":"inactive";

    const handleChange = (event,type) => {
        let temp = stateInput;
        temp[type] = event.target.value;
        setStateInput(temp);
    }

    const { executeRecaptcha } = useGoogleReCaptcha();


    const handleSubmit = (event) => {
        event.preventDefault();
        setEnCours(true);
        setError({
            status:false,
            message:""
        });
        setSuccess({
            status:false,
            message:""
        });
        if (stateInput.Nom == "" || stateInput.Prenom == "" || stateInput.Mail == "" || stateInput.Tel == ""){
            setError({
                status:true,
                message:"Veuillez renseigner tous les champs obligatoires."
            });
            setEnCours(false);
            return false;
        }else{
            let contactToken = null;
            const token = executeRecaptcha("contact_page");
            token.then((result) => {
                contactToken = result;
                fetch("/Systeme/Header/Form.json",
                    {
                        method:"POST",
                        body:JSON.stringify({formValues:stateInput,token:contactToken}),
                        headers:  {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(
                        (result) => {
                            console.log(result);
                            setEnCours(false);
                            if (result.success){
                                setSuccess({
                                    status:true,
                                    message:"Message envoyé avec succès."
                                })
                            }else{
                                setError({
                                    status:true,
                                    message:"Une erreur s'est produite, veuillez réessayer."
                                })
                            }
                        },
                        (error) => {
                            console.log(error);
                            setEnCours(false);
                        }
                    );
            });

        }

    }

    return(
        <div className={"container"}>
            <div className="row justify-content-center">
                <div className="col-md-8 contactHead">
                    <h4>Contactez-nous</h4>
                    <hr/>
                    <h6>Vous avez des questions et aimeriez avoir plus de détails concernant la clinique ou notre équipe ? ?
                        Vous souhaitez prendre rendez-vous avec un de nos praticiens ? Contactez-nous. Notre équipe est à votre
                        écoute pour vous guider</h6>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <ContactError error={error}/>
                </div>
                <form onSubmit={(event) => {handleSubmit(event)}} className={formClass+" col-md-12"}>
                    <div className="row">
                        <div className="col-md-6">
                            <div className="form-group">
                                <label htmlFor="Nom" className={"required"} required={true}>Nom</label>
                                <input id="Nom" className="form-control" type="text" onChange={(event) =>{handleChange(event,"Nom")}}/>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-group">
                                <label htmlFor="Prenom" className={"required"} required={true}>Prénom</label>
                                <input id="Prenom" className="form-control" type="text" onChange={(event) =>{handleChange(event,"Prenom")}}/>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6">
                            <div className="form-group">
                                <label htmlFor="Mail" className={"required"} required={true}>E-mail</label>
                                <input id="Mail" className="form-control" type="text" onChange={(event) =>{handleChange(event,"Mail")}}/>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-group">
                                <label htmlFor="Tel" className={"required"} required={true}>Téléphone</label>
                                <input id="Tel" className="form-control" type="text" onChange={(event) =>{handleChange(event,"Tel")}}/>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6">
                            <div className="form-group">
                                <label htmlFor="Ville">Ville</label>
                                <input id="Ville" className="form-control" type="text" onChange={(event) =>{handleChange(event,"Ville")}}/>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-group">
                                <label htmlFor="Departement">Département</label>
                                <input id="Departement" className="form-control" type="text" onChange={(event) =>{handleChange(event,"Departement")}}/>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-12">
                            <div className="form-group">
                                <label htmlFor="Message">Votre Message</label>
                                <textarea id="Message" className="form-control" onChange={(event) => { handleChange(event, "Message")}}/>
                            </div>
                        </div>
                    </div>
                    <div id={"contactSubmit"} className={"voirplus"}>
                        <input  type="submit" value="Envoyer" />
                    </div>
                </form>
            </div>
            <hr/>
            <div className="row">
                <div className="col-md-6">
                    {adherentData.Adresse}<br/>
                    {adherentData.CodePostal}&nbsp;
                    {adherentData.Ville}
                </div>
                <div className="col-md-6">
                    {adherentData.Tel}<br/>
                    {adherentData.EmailContact}
                </div>
            </div>
            <iframe className="contactMaps"
                    src={"https://www.google.com/maps/embed/v1/place?q="+adherentData.Adresse+" "+adherentData.CodePostal+" "+adherentData.Ville+"&key=AIzaSyA8WzSVq6D0wZFAsNwdsqSExgHlNO3_S68"}
                    allowFullScreen style={{width: "100%", height:"500px"}}>
            </iframe>
        </div>

    )
}

function ContactError (props) {
    let texte = <></>;
    if (props.error.status){
        texte =
            <div className={"error"}>
                {props.error.message}
            </div>
    }
    return (
        <>{texte}</>
    )
}