/**
 * Script for form validation
 * Uses form field data attributes for specific validation rules
 * @param {obj} formObj The current form object
 */
function validate(formObj) {
    var result = 0; /* Bool value to determine form submission */
    
    /* Iterate all enabled input fields */
    $.each($(formObj).find(":enabled"), function(index, value) {

        /* Fields with property required cannot be empty... */
        if($(this).prop("required")) {            
            console.log(this.type);
            
            /* Switch input types for validation type use */
            switch(this.type) {
                case "text":
                case "textarea":
                case "password":
                case "email":
                    if(!$(this).val()) {
                        $(this)[0].setCustomValidity("Feltet må ikke være tomt");
                        $(this)[0].reportValidity();
                        return false;
                    }
                    
                    
                    break;

                case "checkbox":
                    if(!$(this).is(":checked")) {
                        showError($(this), "<br>Du skal acceptere vores betingelser!");
                        result = 0;
                        return false;
                    } else {
                        removeError($(this));
                        result = 1;
                    }                    
                    break;
            }
        }
        
        /* Switch data-validate for specific value validation */
        switch($(this).data("validate")) {
            case "userexist":
                $.ajax({
                    type: "POST",
                    url: "/devs/webshop/assets/scripts/checkusername.php",
                    data: "field=" + $(this).attr("id") + "&username=" + $(this).val(), 
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        if(obj.userexist) {
                            showError($("#" + obj.field), "Brugernavn er optaget! Find et andet!");
                            result = 0;
                            $("#" + obj.field).bind("keydown", function() {
                               removeError($("#" + obj.field));
                               result = 1;
                            });
                            
                            return false;
                        }
                    }
                });                    
                break;            
            
            case "password":
                if(!isValidLength($(this).val(), 8, 20)) {
                    showError($(this), "Adgangskoden skal være mellem 8 og 12 karakterer!");
                    result = 0;
                    return false;
                } else {
                    removeError($(this));
                    result = 1;
                }                
                break;
                
            case "passwordmatch":
                var match = $(this).data("match");
                if($(this).val() != $("#"+match).val()) {
                    showError($(this), "Adgangskoder er ikke ens!");
                    result = 0;
                    return false;
                } else {
                    removeError($(this));
                    result = 1;
                }
                break;
                
            case "onlyalpha":
                if(!isValidAlpha($(this).val())) {
                    showError($(this), "Der må ikke være tal i dette felt!");
                    result = 0;
                    return false;
                } else {
                    removeError($(this));
                    result = 1;
                }
                break;
            
            case "validemail":
                if(!isValidEmail($(this).val())) {
                    showError($(this), "Email adressen er ikke gyldig!");
                    result = 0;
                    return false;
                } else {
                    removeError($(this));
                    result = 1;
                }
                break;            
        }
        
    });
    
    if(result) {
        formObj.submit();
    }
}

/* Function to display an error */
function showError(elm, msg) {
    if(!elm.next().hasClass("text-danger")) {
        $(elm).after("<span class=\"small text-danger\">"+msg+"</span>");
        $(elm).parent().addClass("has-error");
    }
}

/* Function to remove an error  */
function removeError(elm) {
    if($(elm).next().hasClass("text-danger")) {
        $(elm).next().remove();
        $(elm).parent().removeClass("has-error");
    }    
}

/* RegEx & Matching functions  */

/* Tjekker om værdi er et nummer */
function isValidNumber(value) {
    var pattern = /^[0-9]+$/;
    return pattern.test(value);
}

/* Tjekker om værdi er alfabet */
function isValidAlpha(value) {
    var pattern = /^[A-ZÆØÅa-zæøå ]+$/;
    return pattern.test(value);
}

/*Tjekker om værdi har en gyldig email syntaks */
function isValidEmail(value) {
    var pattern = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return pattern.test(value);    
}

/*Tjekker at værdi har en gyldig lændge */
function isValidLength(value, min, max) {
    var pattern = RegExp('^[0-9A-Za-z@#$%]{'+min+','+max+'}$');
    return pattern.test(value);
}