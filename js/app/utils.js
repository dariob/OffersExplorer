/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


utils = function(){};

utils.isExpired = function(timestamp){
    var preimpostata = new Date(timestamp*1000); //30 APRILE 2092
    var oggi = new Date();
     
    var diff = preimpostata.getTime()  - oggi.getTime();
     
    //Se la data preimpostata è già passata
    if(diff <= 0){
        return 'expired-offer';
    }
    //Se la data preimpostata non è ancora passata
    else {
        return 'active-offer';
    }
};


utils.convertToSlug = function(text)
{
    return Text
    .toLowerCase()
    .replace(/[^\w ]+/g,'')
    .replace(/ +/g,'-')
;
}