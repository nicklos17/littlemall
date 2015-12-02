define(function(require, exports, module){

    var showProvinces = function(callback){
        $('#provinces').html('');
        $.ajax({
            type: "POST",
            url: "/region/getProvinces",
            success: function(msg) {
                var provinces = $.parseJSON(msg),
                    length = provinces.length,
                    provincesBk = '<option value="">请选择省份</option> ';
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
                    citiesBk = '<option value="">请选择城市</option> ';
                for(var i=0; i<length; i++){
                    citiesBk += "<option value='" + cities[i].city_id + "' data='" +cities[i].city_code+"'>" + cities[i].city_name + "</option>";
                }
                $('#cities').append(citiesBk);
                if($('#cities option').length == 2){
                    $('#cities option:last').attr('selected', 'selected')
                    $('#cities').attr('data', $('#cities').val())
                    showDistricts()
                }
                var value = $('#cities').attr('data') ? $('#cities').attr('data') : 0;
                if(value){
                    $('#cities option[value='+value+']').attr('selected', 'selected');
                }

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
                    districtsBk = '<option value="">请选择区/县</option> ';
                for(var i=0; i<length; i++){
                    districtsBk += "<option value='" + districts[i].dis_id + "' data='"+districts[i].dis_code+"'>" + districts[i].dis_name + "</option>";
                }
                $('#districts').append(districtsBk);
                if($('#districts option').length == 2){
                    $('#districts option:last').attr('selected', 'selected')
                    $('#districts').attr('data', $('#districts').val())
                }
                var value = $('#districts').attr('data') ? $('#districts').attr('data') : 0;
                if(value){
                    $('#districts option[value='+value+']').attr('selected', 'selected');
                }

                if(typeof callback === 'function'){
                    callback();
                }
            }
        });
    }

    var show = function(callback1, callback2, callback3){
        showProvinces(callback1);
        showCities(callback2);
        showDistricts(callback3);
    }

    var cascade = function(){
        $('#provinces').on('change', function() {
            var proId = $(this).val();
            if(!proId) return false;
            $(this).attr('data', proId);
            $('#cities').html('');
            $('#districts').html('<option value="">请选择区/县</option> ');
            showCities();
        });

        $('#cities').on('change', function() {
            var cityId = $(this).val();
            if(!cityId) return false;
            $(this).attr('data', cityId);
            $('#districts').html('');
            showDistricts();
        });
    }

    exports.show = show;
    exports.cascade = cascade;
    exports.showProvinces = showProvinces;
    exports.showCities = showCities;

});