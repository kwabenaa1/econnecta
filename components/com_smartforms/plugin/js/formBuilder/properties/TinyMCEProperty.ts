import {ElementPropertiesBase} from "./ElementPropertiesBase";
import {RedNaoIconSelector} from "./RedNaoIconSelector";

export class TinyMCEProperty extends ElementPropertiesBase {

    constructor(formelement, propertiesObject, propertyName, propertyTitle, additionalInformation) {
        super(formelement, propertiesObject, propertyName, propertyTitle, additionalInformation);



    }


    public InternalGenerateHtml($fieldContainer:JQuery) {
        let textarea=document.createElement('textarea');
        textarea.innerHTML=this.PropertiesObject[this.PropertyName];
        $fieldContainer.append(textarea);

        rntinymce.init({
            plugins: "code link table textcolor",
            menubar: false,
            toolbar: "styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | fontsizeselect | code link forecolor ",
            target:textarea,
            setup:  (ed) =>{
                ed.on('change', (e) =>{
                    this.Manipulator.SetValue(this.PropertiesObject, this.PropertyName, e.target.getContent(), this.AdditionalInformation);
                    this.RefreshElement();
                });
            }
        });
    }


}
