function r(t,a={}){let n=(window.translations||{})[t]||t;return Object.keys(a).forEach(function(o){n=n.replace(new RegExp(":"+o,"g"),a[o])}),n}export{r as _};
