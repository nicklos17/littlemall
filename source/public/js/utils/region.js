define(function(require, exports, module){

    var showProvinces = function(callback){
        $('#provinces').html('');
        $.ajax({
            type: "POST",
            url: "/region/getProvinces",
            success: function(msg) {
                var provinces = $.parseJSON(msg),
                    length = provinces.length,
                    provincesBk = '<option value="">省份</option> ';
                for(var i=0; i<length; i++){
                    provincesBk += "<option value='" + provinces[i].pro_id + "' data='"  +provinces[i].pro_code+ "'>" + provinces[i].pro_name + "</option>";
                }
                $('#provinces').append(provincesBk);
                var value = $('#provinces').attr('data') ? $('#provinces').attr('data') : 0;
                $('#provinces option[value='+value+']').attr('selected', 'selected');
                if(typeof callback === 'function'){
                    callback();
                }
            }
        });
    }

    var showCities = function(callback){
        $('#cities').html('');
        $.ajax({
            type: "POST",
            url: "/region/getCities",
            data: {pro_id: $('#provinces').attr('data')},
            success: function(msg) {
                var cities   = $.parseJSON(msg),
                    length   = cities.length,
                    citiesBk = '<option value="">城市</option> ';
                for(var i=0; i<length; i++){
                    citiesBk += "<option value='" + cities[i].city_id + "' data='" +cities[i].city_code+"'>" + cities[i].city_name + "</option>";
                }
                $('#cities').append(citiesBk);
                var value = $('#cities').attr('data') ? $('#cities').attr('data') : 0;
                $('#cities option[value='+value+']').attr('selected', 'selected');
                if(typeof callback === 'function'){
                    callback();
                }
            }
        });
    }

    var showDistricts = function(callback){
        $('#districts').html('');
        $.ajax({
            type: "POST",
            url: "/region/getDistricts",
            data: {city_id: $('#cities').attr('data')},
            success: function(msg) {
                var districts = $.parseJSON(msg),
                    length = districts.length,
                    districtsBk = '<option value="">县区</option> ';
                for(var i=0; i<length; i++){
                    districtsBk += "<option value='" + districts[i].dis_id + "' data='"+districts[i].dis_code+"'>" + districts[i].dis_name + "</option>";
                }
                $('#districts').append(districtsBk);
                var value = $('#districts').attr('data') ? $('#districts').attr('data') : 0;
                $('#districts option[value='+value+']').attr('selected', 'selected');
                if(typeof callback === 'function'){
                    callback();
                }
            }
        });
    }

    var showStreets = function(callback){
        $('#streets').html('');
        $.ajax({
            type: "POST",
            url: "/region/getStreets",
            data: {dis_id: $('#districts').attr('data')},
            success: function(msg) {
                var streets = $.parseJSON(msg),
                    length = streets.length,
                    streetsBk = '<option value="">街道</option> ';
                for(var i=0; i<length; i++){
                    streetsBk += "<option value='" + streets[i].street_id + "' data='"+streets[i].street_code+"'>" + streets[i].street_name + "</option>"
                }
                $('#streets').append(streetsBk);
                var value = $('#streets').attr('data') ? $('#streets').attr('data') : 0;
                $('#streets option[value='+value+']').attr('selected', 'selected');
                if(typeof callback === 'function'){
                    callback();
                }
            }
        });
    }

    var show = function(callback1, callback2, callback3, callback4){
        showProvinces(callback1);
        showCities(callback2);
        showDistricts(callback3);
        showStreets(callback4);
    }

    var cascade = function(){
        $('#provinces').on('change', function() {
            var proId = $(this).val();
            if(!proId) return false;
            $(this).attr('data', proId);
            $('#cities').html('');
            $('#districts').html('');
            $('#streets').html('');
            showCities();
        });

        $('#cities').on('change', function() {
            var cityId = $(this).val();
            if(!cityId) return false;
            $(this).attr('data', cityId);
            $('#districts').html('');
            $('#streets').html('');
            showDistricts();
        });

        $('#districts').on('change', function() {
            var disId = $(this).val();
            if(!disId) return false;
            $(this).attr('data', disId);
            $('#streets').html('');
            showStreets();
        });
    }

    exports.show = show;
    exports.cascade = cascade;
    exports.showProvinces = showProvinces;
    exports.showCities = showCities;
    exports.showDistricts = showDistricts;
    exports.showStreets = showStreets;

});