/*
 ╭──────────────────╮
 │\u shall not pass!│       
 ╯──────────────────╯
*/

const maxChar = 512;

$(document).ready(function() {
    var inputs = document.getElementsByTagName('input');
    
    for (var i= 0; i < inputs.length; i++) {
        $(inputs[i]).keyup(function(e) {
            
            var entity = e.currentTarget;   

            if(aBalrog(entity.value)){      
                entity.value = '';
            }

        });
    };

});


function aBalrog(string){

    if(string == '' || string == null){
        return false;
    }

    var recievedCharacters = string.split('');

    for(i = 0; i < recievedCharacters.length; i++){
        if (recievedCharacters[i].charCodeAt(0) > maxChar){
            return true;
        }
    }

    return false;
}