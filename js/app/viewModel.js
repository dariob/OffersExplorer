viewModel = {

    user: ko.observable({}),
    offers: ko.observableArray([]),
    pages: ko.observableArray([]),
    likes: ko.observableArray([]),
    
    
    offerDetailView: ko.observable(),
    
    
    offerDetailView_old: function(o,e){
        console.log(o,e);
       
        offerIndex = jQuery.inArray(o, viewModel.offers()); 
        console.log(offerIndex);
        return viewModel.offers()[offerIndex]
    },
    
    
    
    offersContainer: jQuery("#offersContainer"),
    
    totalOfferShow: ko.observable(),
    
    filterActiveOffers: function(o,e){
        // jQuery(e.currentTarget).addClass("btn-inverse").find("i").addClass("icon-white");
        this.offersContainer.isotope({
            filter: '.active-offer'
        });
    },
    filterExpiredOffers: function(){
        this.offersContainer.isotope({
            filter:  '.expired-offer' 
        });
    }

};

ko.applyBindings(viewModel);