var aa=function(){function a(){this.value=[];this.i=0}function d(a,b){return a-b}function f(a,d,f){return new Promise(function(c){var k=g(a);crypto.subtle.digest("SHA-256",b.encode(k)).then(function(a){a=(new DataView(a)).getUint32(0);var b=Math.log2(d*f);if(31<b)throw Error("This implementation only supports up to 31 bit hash values");c(a>>32-b&(1<<b)-1)})})}function g(a){return a.replace(/[!'()*]/g,function(a){return"%"+a.charCodeAt(0).toString(16)})}function g(a){return a.replace(/[!'()*]/g,function(a){return"%"+
a.charCodeAt(0).toString(16)})}function m(b,k){if(k>=Math.pow(2,32))throw Error('Invalid probability: "${p}" must be smaller than 2**32');if(!(0<k&&(k&~k+1)===k))throw Error('Invalid probability: "${p}" must be a power of 2');var c,g=Math.min(Math.pow(2,Math.round(Math.log2(b.length))),Math.pow(2,31)),m=[];return new Promise(function(q){Promise.all(b.map(function(a){return f(a,g,k)})).then(function(){m=m.concat().sort(d);c=Uint8Array.from((new a).l(Math.log2(g),5).l(Math.log2(k),5).H(m,Math.log2(k)).value);
var b;b="";for(var f=c.byteLength,r=0;r<f;r++)b+=String.fromCharCode(c[r]);b=btoa(b).replace(/=+$/,"");q(b)})})}a.prototype.j=function(a){0==this.i&&(this.value.push(0),this.i=8);--this.i;a&&(this.value[this.value.length-1]|=1<<this.i)};a.prototype.l=function(a,b){if(0!=b){do--b,this.j(a&1<<b);while(0!=b)}return this};a.prototype.H=function(a,b){for(var c=-1,d=0;d!=a.length;++d)if(c!=a[d]){for(var c=a[d]-c-1,f=c>>b;0!=f;--f)this.j(0);this.j(1);this.l(c,b);c=a[d]}return this};var b=new TextEncoder("utf-8");
return function(a,b){return m(a,b)}}();
self.u.push([function(a){var d=a[0];return PROMISE(function(f){var g=d.headers.get("accept");g&&g.includes("text/html")?self.g(":push").then(function(g){g.keys().then(function(b){if(0===b.length)f(a);else{var c=[];b.forEach(function(a){c.push(CACHE_GET(a))});Promise.all(c).then(function(c){var g=[];b.forEach(function(a,b){"undefined"!==c[b]&&c[b]&&g.push(a.url)});0===g.length?f(a):aa(g,Math.pow(2,7)).then(function(b){b&&d.headers.set("cache-digest",b);f(a)})})}})}):f(a)})},function(a){return PROMISE(function(d){if(a.ok&&
400>a.status){var f=a.headers.get("link");f&&(f instanceof Array||(f=[f]),self.g(":push").then(function(a){f.forEach(function(d){d.split(",").forEach(function(b){if(/rel=preload/.test(b)){var c=b.match(/<([^>]+)>/);c&&c[1]&&a.match(c[1]).then(function(b){b||a.put(c[1],new Response(null,{status:204}))})}})})}))}d(a)})}]);