
//根据价格获得,能选择的策略,返回 array(object(aid,index,price,item,title));
function getActiveByPrice(price,activitys){
	var endActives = [];  //最终组合策略结果
   	var minPrice = price;
   	var minActive;
   	
	for(var i in activitys){
		var activity = activitys[i];
		var actives = activity.active;
		var aid = activity.aid;
		for(var j in actives){
			var active = actives[j];
			var tempActive = {};
			//判断是否满足条件
			if(checkActive(price,active)){
				//价格进行计算
				if(active.type == 'price'){
					newPrice = priceByActive(price,active);
					tempActive.price = newPrice;
					tempActive.index = j;
					tempActive.title = showActiveTitle(active);
					tempActive.aid = aid;
					if(newPrice < minPrice){
						minPrice = newPrice;
						endActives.unshift(tempActive);   //价格小的排第一个去
					}else{
						endActives.push(tempActive);
					}
				}else if(active.type == 'item'){
					//其他类型的直接整合进去
					tempActive.price = price;
					tempActive.index = j;
					tempActive.title = showActiveTitle(active);
					tempActive.aid = aid;
					tempActive.item = active.params.iname;
					endActives.push(tempActive);
				}
			}
		}
	}
	
	return endActives;
}

//根据不同策略判断是否满足策略条件,目前只有价格判断
function checkActive(price,active){
	params = active.params;
	needprice = parseFloat(params.p);
	return price >= needprice;
}

//价格策略根据不同策略算出结果
function priceByActive(price,active){
   	name = active.name;
   	params = active.params;
   	price = Math.round(price*100);    //转成分
   	newPrice = price;
   	switch (name){
   		case 'PriceManJian':
   			newPrice = price - Math.round(params.jian*100);
   			break;
   		case 'PriceMeiManJian':
   			newPrice = price - parseInt(price/(params.p*100)) * params.jian * 100;
   			break;
   		case 'PriceManZhe':
   			newPrice = price * params.zhe/10;
   			break;
   	}
   	return (Math.round(newPrice)*0.01).toFixed(2);
}

//根据策略显示策略名称
function showActiveTitle(active){
   	name = active.name;
   	params = active.params;
   	title = "";
   	switch (name){
   		case 'PriceManJian':
   			title = "满"+params.p+"元减"+params.jian+"元";
   			break;
   		case 'PriceMeiManJian':
   			title = "每满"+params.p+"元减"+params.jian+"元";
   			break;
   		case 'PriceManZhe':
   			title = "满"+params.p+"元打"+params.zhe+"折";
   			break;
   		case 'PriceManItem':
   			title = "满"+params.p+"元送"+params.iname;
   			break;
   	}
   	return title;
}