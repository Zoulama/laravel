var SBH = {};

SBH.VAR = {};
SBH.FUN = {};

// console.log pour le debug
SBH.d = function() {
    var i = 0,
        arg;

    for (; i < arguments.length ; i++) {
        arg = arguments[i];

        console.log("[DEBUG]", arg);
    }
}

// console.log pour le debug des requêtes AJAX
SBH.a = function(query, response, label) {
    if (typeof query !== 'undefined' && typeof response !== 'undefined') {
        if (typeof label !== 'undefined') {
            console.log("[AJAX - "+label+"]", query, response);
        }
        else {
            console.log("[AJAX]", query, response);
        }
    }
}

SBH.UTILS = {};

// slt ça va => Slt ça va
SBH.UTILS.ucFirst = function(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}