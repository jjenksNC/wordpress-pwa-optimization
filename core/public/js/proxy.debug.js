var Ob=/^\/(.*)\/([gimuy]+)?$/;function Pb(a){if(a=a.match(Ob)){try{var c=new RegExp(a[1],a[2])}catch(b){}return c||!1}}var hb={},Qb=x[2]?x[2][6]:!1,Rb=x[2]?x[2][14]:!1,Sb=Qb||Rb?!0:!1,Tb=x[3]?x[3][6]:!1,Ub=x[3]?x[3][14]:!1,Vb=Tb||Ub?!0:!1;function Ua(a,c,b){return xa(a,c+"/proxy/","."+c,b)}
function Wb(a){if(a&&a.nodeName){var c=a.nodeName;if(Sb&&"LINK"===c&&a.href&&a.rel&&"stylesheet"===a.rel.toLowerCase()){var b=a.href,d=c=!1;if(!(b in Ja))if(Qb&&b in Qb)d=Qb[b];else if(Rb)for(var e=0,f=Rb.length;e<f;e++)if(Pb(Rb[e][0]).test(b)){d=Rb[e][1];c=Pb(Rb[e][0]);break}if(d){var g=Ua(d,"css"),d={proxy:[F(g)]};c&&(d.regex=c);E(a,{href:g,"data-src":b});if(eb){E(a,{rel:"preload",as:"style"});Q[g]=a;var k,c=function(){E(a,{"data-load":1});k||(k=!0,CSS_ASYNC_LOAD(g,a.media,CSS_RENDER_POSITION?!0:
!1,eb))};La(a.media)?(b={capture:b,async:!0},J("css.proxy",b,d),a.onload=c,ra("load",c,a)):(b={capture:b},d.responsive=a.media,J("css.proxy",b,d),Pa(a.media,c))}else b={capture:b},J("css.proxy",b,d)}}else if(Vb&&"SCRIPT"===c&&a.src){var h=a.src,d=c=!1;if(Tb&&b in Tb)d=Tb[b];else if(Ub)for(e=0,f=Ub.length;e<f;e++)if(Pb(Ub[e][0]).test(h)){d=Ub[e][1];c=Pb(Ub[e][0]);break}d&&(a.src=Ua(d,"js"),d={proxy:[F(a.src)]},c&&(d.regex=c),b={capture:h},J("js.proxy",b,d))}}return a}
if(Sb||Vb){var hb=x[6]&&x[6][0]?x[6][0]:{},Xb={},Yb={},X={Element:"undefined"!==typeof Element?Element:!1,Document:"undefined"!==typeof Document?Document:!1},Y;for(Y in X)X.hasOwnProperty(Y)&&X[Y]&&(Xb[Y]=X[Y].prototype.appendChild,Yb[Y]=X[Y].prototype.insertBefore);for(Y in X)X.hasOwnProperty(Y)&&X[Y]&&function(a,c){c.prototype.appendChild=function(b){b=Wb(b);return Xb[a].call(this,b)};c.prototype.insertBefore=function(b,c){var d;d=Wb(b);return Yb[a].call(this,d,c)}}(Y,X[Y])};