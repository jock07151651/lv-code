var MarketTS = function () {
	this._get = function (__stringId) {
		return document.getElementById(__stringId);
	};
	this._random = function () {
		return (new Date()).getTime() + Math.random().toString().replace("0.", "");
	};
	this._bind = function (__functionBind, __argumentsBind) {
		var __this = this;
		return function () {
			var __arguments = null;
			if (typeof __argumentsBind != "undefined") {
				for (var i = 0; i < arguments.length; i++) {
					__argumentsBind.push(arguments[i]);
				}
				__arguments = __argumentsBind;
			}
			else {
				__arguments = arguments;
			}
			return __functionBind.apply(__this, __arguments);
		};
	};
	this._aevent = function (__elementTarget, __stringType, __functionEvent) {
		if (window.addEventListener) {
			__elementTarget.addEventListener(__stringType, __functionEvent, false);
		} else if (window.attachEvent) {
			__elementTarget.attachEvent("on" + __stringType, __functionEvent);
		}
	};
	this._count = function () {
		if (this._dateUTC != null) {
			this._dateUTC = new Date(this._dateUTC.getTime() + 1000);
		}
	};
	this._load = function (__stringUrl, __functionCallback) {
		var __elementScript = document.createElement("script");
		__elementScript.type = "text/javascript";
		__elementScript._functionCallback = typeof __functionCallback != "undefined" ?  __functionCallback : new Function();
		__elementScript[document.all ? "onreadystatechange" : "onload"] = function () {
			if (document.all && this.readyState != "loaded" && this.readyState != "complete") {return;}
			this._functionCallback(this);
			this._functionCallback = null;
			this[document.all ? "onreadystatechange" : "onload"] = null;
			this.parentNode.removeChild(this);
		};
		__elementScript.src = __stringUrl;
		document.getElementsByTagName("head")[0].appendChild(__elementScript);
	};
	this._fixZero = function (__number) {
		return (__number * 1 < 10 ? "0" : "") + __number.toString();
	};
	this._getFullTime = function (__dateTime) {
		return __dateTime.getFullYear() + "-" + this._fixZero((__dateTime.getMonth() * 1) + 1) + "-" + this._fixZero(__dateTime.getDate()) + " " + this._fixZero(__dateTime.getHours()) + ":" + this._fixZero(__dateTime.getMinutes()) + ":" + this._fixZero(__dateTime.getSeconds());
	};
	this._changeTimezone = function (__date, __numbetTimezone) {
		var __dateUTC = new Date(__date.getTime() + __numbetTimezone * 60 * 60 * 1000);
		var __dateLocale = new Date(__dateUTC.getUTCFullYear(), __dateUTC.getUTCMonth(), __dateUTC.getUTCDate(), __dateUTC.getUTCHours(), __dateUTC.getUTCMinutes(), __dateUTC.getUTCSeconds());
		return __dateLocale;
	};
	/* bind config */
	this._bindMarkets = function (__stringArea, __stringMarket, __functionFixTime, __functionFixStatus) {
		var __stringKey = "market_" + __stringArea + "_" + __stringMarket;
		if (!(__stringKey in this._objectMarkets)) {
			this._objectMarkets[__stringKey] = {};
		}
		this._objectMarkets[__stringKey]._stringArea = __stringArea;
		this._objectMarkets[__stringKey]._stringMarket = __stringMarket;
		this._objectMarkets[__stringKey]._functionFixTime = __functionFixTime;
		this._objectMarkets[__stringKey]._functionFixStatus = __functionFixStatus;
		//~  this._debug("bind market (" + __stringArea + ")" + __stringMarket);
		//~  this._loadList();
		if (this._threadListInit != -1) {
			clearTimeout(this._threadListInit);
		}
		this._threadListInit = setTimeout(this._bind(this._loadList), 50);
	};
	/* sina time */
	this._loadTime = function () {
		this._load(this._stringTimeUrl.replace("@RANDOM@", this._random()), this._bind(this._fixTime));
	};
	this._fixTime = function () {
		if (this._stringTimeKey in window) {
			this._dateUTC = new Date(window[this._stringTimeKey] * 1000);
			this._debug("set system's server time to " + this._dateUTC.toUTCString() + " UTC");
			this._makeTimeout();
		}
	};
	/* market data */
	this._loadList = function () {
		var __arrayMarkets = [];
		for (var i in this._objectMarkets) {
			__arrayMarkets.push(i);
		}
		if (__arrayMarkets.length > 0) {
			this._load(this._stringHqUrl.replace("@LIST@", __arrayMarkets.join(",")).replace("@RANDOM@", this._random()), this._bind(this._processList));
		}
	};
	this._processList = function () {
		for (var i in this._objectMarkets) {
			if ("hq_str_" + i in window && window["hq_str_" + i] != "") {
				var __stringData = window["hq_str_" + i];
				var __objectMarket = this._objectMarkets[i];
				__objectMarket._booleanNeedUpdate = __objectMarket._stringData != __stringData;
				if (__objectMarket._booleanNeedUpdate) {
					__objectMarket._stringData = __stringData;
					var __arrayData = __stringData.split("|");
					//处理时区
					if (typeof __objectMarket._arrayTimeConfig != "undefined") {
						for (var j in __objectMarket._arrayTimeConfig) {
							if (__objectMarket._arrayTimeConfig[j][0] != -1) {
								clearTimeout(__objectMarket._arrayTimeConfig[j][0]);
								__objectMarket._arrayTimeConfig[j][0] = -1;
							}
						}
					}
					__objectMarket._stringTimezoneOffset = null;
					__objectMarket._arrayTimeConfig = [];
					
					var __arrayTimeConfig = __arrayData[0].replace(/;$/, "").split(";");
					for (var j in __arrayTimeConfig) {
						var __arrayTimeConfigContent = __arrayTimeConfig[j].split(",");
						if (__objectMarket._stringTimezoneOffset == null) {
							__objectMarket._stringTimezoneOffset = __arrayTimeConfigContent[2];
						}
						var __arrayDate = __arrayTimeConfigContent[0].split("-");
						var __arrayTime = __arrayTimeConfigContent[1].split(":");
						var __dateNewDate = new Date(__arrayDate[0] * 1, __arrayDate[1] * 1 - 1, __arrayDate[2] * 1, __arrayTime[0] * 1, __arrayTime[1] * 1, __arrayTime[2] * 1);
						__objectMarket._arrayTimeConfig.push([-1, __dateNewDate, __arrayTimeConfigContent[2]]);
					}
					
					//处理状态
					if (typeof __objectMarket._arrayStatusConfig != "undefined") {
						for (var j in __objectMarket._arrayStatusConfig) {
							if (__objectMarket._arrayStatusConfig[j][2] != -1) {
								clearTimeout(__objectMarket._arrayStatusConfig[j][2]);
								__objectMarket._arrayStatusConfig[j][2] = -1;
							}
							if (__objectMarket._arrayStatusConfig[j][5] != -1) {
								clearTimeout(__objectMarket._arrayStatusConfig[j][5]);
								__objectMarket._arrayStatusConfig[j][5] = -1;
							}
						}
					}
					__objectMarket._arrayStatusConfig = [];
					
					var __arrayStatusConfig = __arrayData[1].replace(/;$/, "").split(";");
					for (var j in __arrayStatusConfig) {
						var __arrayStatusConfigContent = __arrayStatusConfig[j].split(",");
						if (__arrayStatusConfigContent[0] == "*") {
							__objectMarket._arrayStatusConfig.push([0, "0", -1, __arrayStatusConfigContent[1], null, -1, __arrayStatusConfigContent[2], null, __arrayStatusConfigContent[3]]);
							__objectMarket._arrayStatusConfig.push([0, "+1", -1, __arrayStatusConfigContent[1], null, -1, __arrayStatusConfigContent[2], null, __arrayStatusConfigContent[3]]);
						}
						else if (__arrayStatusConfigContent[0].substr(0, 1) == "w") {
							__objectMarket._arrayStatusConfig.push([1, __arrayStatusConfigContent[0].substr(1, 1), -1, __arrayStatusConfigContent[1], null, -1, __arrayStatusConfigContent[2], null, __arrayStatusConfigContent[3]]);
						}
						else {
							__objectMarket._arrayStatusConfig.push([2, __arrayStatusConfigContent[0], -1, __arrayStatusConfigContent[1], null, -1, __arrayStatusConfigContent[2], null, __arrayStatusConfigContent[3]]);
						}
					}
				}
				else {
				}
			}
		}
		this._makeTimeout();
	};
	/* make timeout table */
	this._makeTimeout = function () {
		if (this._dateUTC != null) {
			for (var i in this._objectMarkets) {
				var __objectMarket = this._objectMarkets[i];
				if (typeof __objectMarket._booleanNeedUpdate != "undefined" && __objectMarket._booleanNeedUpdate == true) {
					__objectMarket._booleanNeedUpdate = false;
					if (typeof __objectMarket._stringTimezoneOffset != "undefined") {
						var __dateNow = this._changeTimezone(this._dateUTC, __objectMarket._stringTimezoneOffset * 1);
						this._debug("set (" + i.split("_")[1] + ")" + i.split("_")[2] + "'s local time to " + this._getFullTime(__dateNow) + " (" + __objectMarket._stringTimezoneOffset + ")");
						__objectMarket._functionFixTime(__dateNow);
						if (typeof __objectMarket._arrayTimeConfig != "undefined") {
							var __arrayTimeConfig = __objectMarket._arrayTimeConfig;
							var __arrayTimeConfigNow = null;
							for (var j in __arrayTimeConfig) {
								var __numbetMS = __arrayTimeConfig[j][1].getTime() - __dateNow.getTime();
								if (__numbetMS >= 0 && __numbetMS < this._numberMaxTimeout) {
									this._debug("set (" + i.split("_")[1] + ")" + i.split("_")[2] + "'s timezone to " + __arrayTimeConfig[j][2] + " after "+ (__numbetMS / 1000) + " second");
									__arrayTimeConfig[j][0] = setTimeout(this._bind(this._changeMarketTimzoneOffset, [__objectMarket, __arrayTimeConfig[j]]), __numbetMS);
								}
								else {
									__arrayTimeConfigNow = __arrayTimeConfig[j];
								}
							}
							this._debug("set (" + i.split("_")[1] + ")" + i.split("_")[2] + "'s timezone to " + __arrayTimeConfigNow[2] + " now");
							__objectMarket._functionFixTime(this._changeTimezone(this._dateUTC, __arrayTimeConfigNow[2] * 1));
						}
						if (typeof __objectMarket._arrayStatusConfig != "undefined") {
							var __arrayStatusConfig = __objectMarket._arrayStatusConfig;
							var __arrayStatusConfigNow = null;
							for (var j in __arrayStatusConfig) {
								switch (__arrayStatusConfig[j][0]) {
									case 0:
										var __dateTheDate = new Date(__dateNow.getFullYear(), __dateNow.getMonth(), __dateNow.getDate() * 1 + __arrayStatusConfig[j][1] * 1);
										var __arrayTimeBegin = __arrayStatusConfig[j][3].split(":");
										var __arrayTimeEnd = __arrayStatusConfig[j][6].split(":");
										__arrayStatusConfig[j][1] = __dateTheDate.getFullYear() + "-" + ((__dateTheDate.getMonth() * 1) + 1 < 10 ? "0" : "") + (__dateTheDate.getMonth() + 1) + "-" + (__dateTheDate.getDate() * 1 < 10 ? "0" : "") + __dateTheDate.getDate();
										break;
									case 1:
										var __numberDay = __dateNow.getDay();
										var __numberTheDay = __arrayStatusConfig[j][1] * 1;
										var __numberDayBetween = __numberTheDay - __numberDay;
										if (__numberDayBetween < 0) {
											__numberDayBetween += 7;
										}
										var __dateTheDate = new Date(__dateNow.getFullYear(), __dateNow.getMonth(), __dateNow.getDate() * 1 + __numberDayBetween);
										var __arrayTimeBegin = __arrayStatusConfig[j][3].split(":");
										var __arrayTimeEnd = __arrayStatusConfig[j][6].split(":");
										__arrayStatusConfig[j][1] = __dateTheDate.getFullYear() + "-" + ((__dateTheDate.getMonth() * 1) + 1 < 10 ? "0" : "") + (__dateTheDate.getMonth() + 1) + "-" + (__dateTheDate.getDate() * 1 < 10 ? "0" : "") + __dateTheDate.getDate();
										break;
									default:
										var __arrayDate = __arrayStatusConfig[j][1].split("-");
										var __arrayTimeBegin = __arrayStatusConfig[j][3].split(":");
										var __dateTheDate = new Date(__arrayDate[0] * 1, __arrayDate[1] * 1 - 1, __arrayDate[2]);
										var __arrayTimeEnd = __arrayStatusConfig[j][6].split(":");
										break;
								}
								__arrayStatusConfig[j][4] = new Date(__dateTheDate.getFullYear(), __dateTheDate.getMonth(), __dateTheDate.getDate(), __arrayTimeBegin[0] * 1, __arrayTimeBegin[1] * 1, __arrayTimeBegin[2] * 1);
								__arrayStatusConfig[j][7] = new Date(__dateTheDate.getFullYear(), __dateTheDate.getMonth(), __dateTheDate.getDate(), __arrayTimeEnd[0] * 1, __arrayTimeEnd[1] * 1, __arrayTimeEnd[2] * 1);
								var __numbetMS = __arrayStatusConfig[j][4].getTime() - __dateNow.getTime();
								if (__numbetMS >= 0 && __numbetMS < this._numberMaxTimeout) {
									this._debug("set (" + i.split("_")[1] + ")" + i.split("_")[2] + "'s status to " + __arrayStatusConfig[j][8] + " after "+ (__numbetMS / 1000) + " second (bedin)");
									__arrayStatusConfig[j][2] = setTimeout(this._bind(this._changeMarketStatus, [__objectMarket, __arrayStatusConfig[j], "begin"]), __numbetMS);
								}
								var __numbetMS = __arrayStatusConfig[j][7].getTime() - __dateNow.getTime();
								if (__numbetMS >= 0 && __numbetMS < this._numberMaxTimeout) {
									this._debug("set (" + i.split("_")[1] + ")" + i.split("_")[2] + "'s status to " + __arrayStatusConfig[j][8] + " after "+ (__numbetMS / 1000) + " second (end)");
									__arrayStatusConfig[j][5] = setTimeout(this._bind(this._changeMarketStatus, [__objectMarket, __arrayStatusConfig[j], "end"]), __numbetMS);
								}
								if (__arrayStatusConfigNow == null) {
									__arrayStatusConfigNow = __arrayStatusConfig[j];
								}
								else if (__arrayStatusConfig[j][0] >= __arrayStatusConfigNow[0]) {
									if (__dateNow.getTime() >= __arrayStatusConfig[j][4].getTime() && __dateNow.getTime() < __arrayStatusConfig[j][7].getTime()) {
										__arrayStatusConfigNow = __arrayStatusConfig[j];
									}
								}
							}
							this._debug("set (" + i.split("_")[1] + ")" + i.split("_")[2] + "'s status to " + __arrayStatusConfigNow[8] + " now");
							__objectMarket._functionFixStatus(__arrayStatusConfigNow[8]);
						}
					}
				}
			}
		}
	};
	this._changeMarketTimzoneOffset = function (__objectMarket, __arrayTimeConfigContent) {
		__objectMarket._stringTimezoneOffset = __arrayTimeConfigContent[2];
		__arrayTimeConfigContent[0] = -1;
		this._debug("set (" + __objectMarket._stringArea + ")" + __objectMarket._stringMarket + "'s timezone to " + __arrayTimeConfigContent[2] + " now");
		__objectMarket._functionFixTime(this._changeTimezone(this._dateUTC, __arrayTimeConfigContent[2] * 1));
	};
	this._changeMarketStatus = function (__objectMarket, __arrayStatusConfigContent, __stringStatus) {
		var __arrayStatusConfigNow = __arrayStatusConfigContent;
		var __arrayStatusConfig = __objectMarket._arrayStatusConfig;
		if (__stringStatus == "begin") {
			var __dateNow = __arrayStatusConfigNow[4];
			__arrayStatusConfigContent[2] = -1;
		}
		if (__stringStatus == "end") {
			var __dateNow = __arrayStatusConfigNow[7];
			__arrayStatusConfigContent[5] = -1;
		}
		var __arrayStatusConfigNow = [-1];
		for (var i in __arrayStatusConfig) {
			if (__arrayStatusConfig[i][0] >= __arrayStatusConfigNow[0]) {
				if (__dateNow.getTime() >= __arrayStatusConfig[i][4].getTime() && __dateNow.getTime() < __arrayStatusConfig[i][7].getTime()) {
					__arrayStatusConfigNow = __arrayStatusConfig[i];
				}
			}
		}
		if (__arrayStatusConfigNow[0] == -1) {
			this._debug("none status matched");
			__arrayStatusConfigNow = __arrayStatusConfigContent;
		}
		this._debug("set (" + __objectMarket._stringArea + ")" + __objectMarket._stringMarket + "'s status to " + __arrayStatusConfigNow[8] + " now");
		__objectMarket._functionFixStatus(__arrayStatusConfigNow[8]);
	};
	/* main */
	this._initialize = function (__functionDebug) {
		this["debug"] = typeof __functionDebug != "undefined" ? __functionDebug : function () {};
		this._debug = function (__stringInfo) {
			this["debug"](__stringInfo);
		};
		this._dateUTC = null;
		this._stringTimeUrl = "http://counter.sina.com.cn/time?fm=JS&rn=@RANDOM@";
		this._stringTimeKey = "StandardBJTime";
		this._numberUpdateTimeTimeout = 2 * 60 * 60 * 1000;
		
		this._objectMarkets = {};
		this._stringHqUrl = "http://hq.sinajs.cn/random=@RANDOM@&list=@LIST@";
		this._stringUrl = "";
		this._numberUpdateListTimeout = 10 * 60 * 1000;
		
		this._numberMaxTimeout = Math.max((this._numberUpdateTimeTimeout + this._numberUpdateListTimeout) * 2, 24 * 60 * 60 * 1000);
		
		this._loadTime();
		this._threadServerTime = setInterval(this._bind(this._loadTime), this._numberUpdateTimeTimeout);
		
		this._count();
		setInterval(this._bind(this._count), 1000);
		
		this._threadListInit = -1;
		this._loadList();
		this._threadList = setInterval(this._bind(this._loadList), this._numberUpdateListTimeout);
		
		this["bind"] = this._bindMarkets;
	};
	this._initialize.apply(this, arguments);
};