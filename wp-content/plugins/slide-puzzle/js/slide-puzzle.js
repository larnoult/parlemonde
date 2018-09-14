function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}


function measure(elem) {
    var short_names = {
        mt: 'margin-top',
        btw: 'border-top-width',
        pt: 'padding-top',
        mr: 'margin-right',
        brw: 'border-right-width',
        pr: 'padding-right',
        mb: 'margin-bottom',
        bbw: 'border-bottom-width',
        pb: 'padding-bottom',
        ml: 'margin-left',
        blw: 'border-left-width',
        pl: 'padding-left',
    };
    var style = window.getComputedStyle(elem, null);
    var sum = 0;
    for (var i=1; i<arguments.length; ++i) {
        var arg = arguments[i];
        var expansion = short_names[arg];
        var name = (expansion == undefined ? arg : expansion);
        sum += parseFloat(style.getPropertyValue(name));
    }
    return sum;
}

function setup_drag(elem, get_pos, set_pos, done_cb, cursor) {
    function down_cb(e) {
        var pos = get_pos();
        var dx = e.pageX - pos[0];
        var dy = e.pageY - pos[1];
        function move_cb(e) {
            set_pos(e.pageX - dx, e.pageY - dy);
            e.stopPropagation();
            e.preventDefault();
        }
        function up_cb(e) {
            document.removeEventListener("mousemove", move_cb, true);
            document.removeEventListener("mouseup", up_cb, true);
            if (cursor) document.body.style.cursor = "default";
            if (done_cb) done_cb();
        }
        document.addEventListener("mousemove", move_cb, true);
        document.addEventListener("mouseup", up_cb, true);
        if (cursor) document.body.style.cursor = "move";
        e.stopPropagation();
        e.preventDefault();
    }
    elem.addEventListener("mousedown", down_cb, false);
}

function dialog(title, body, buttons, callback) {
    var dialog = document.createElement('div');
    dialog.className = "dialog";
    var str = '<div class="titlebar"><h1>'+title+'</h1></div>\n';
    str += body+'\n';
    str += '<p class="buttons">'
    dialog.innerHTML = str;

    var pb = dialog.lastChild;
    function get_cb(val) {
        return function() {
            dialog.parentNode.removeChild(dialog);
            callback(val);
        };
    }
    for (var i=0; i<buttons.length; ++i) {
        var b = document.createElement("button");
        b.setAttribute("type", "button");
        b.innerHTML = buttons[i];
        b.onclick = get_cb(buttons[i]);
        pb.appendChild(b);
    }

    document.body.appendChild(dialog);
    pb.lastChild.focus();
    setup_drag(dialog.firstChild,
               function() { return [ dialog.offsetLeft, dialog.offsetTop ]; },
               function(x, y) { dialog.style.left = x+"px";
                                dialog.style.top = y+"px"; });
}

var slide_tab = [ 0.1, 0.2, 0.6, 0.9, 1.0 ];
function slide_elem(elem, x1, y1) {
    var x0 = elem.offsetLeft;
    var y0 = elem.offsetTop;
    var i = 0;

    function move_it() {
        var q = slide_tab[i];
        elem.style.left = (q*x1+(1-q)*x0)+"px";
        elem.style.top = (q*y1+(1-q)*y0)+"px";
        i += 1;
        return i < slide_tab.length;
    }
    function move_cb() {
        if (move_it()) setTimeout(move_cb, 10);
    }
    move_cb();

    try {
        sound.pause();
        sound.currentTime = 0;
        sound.play();
    } catch (err) {
    }
}

function random_permutation(N) {
    var pos = new Array();
    pos.push(0);
    for (var i=1; i<N; ++i) {
        var j = Math.floor(Math.random()*(i+1))
        if (j<i) {
            pos.push(pos[j]);
            pos[j] = i;
        } else {
            pos.push(i);
        }
    }
    return pos
}

function parity(perm) {
    var res = 1;
    for (var i=1; i<perm.length; ++i) {
        for (var j=0; j<i; ++j) {
            if (perm[i] < perm[j]) res = -res;
        }
    }
    return res;
}

function is_identity(pos) {
    for (i=0 ; i<pos.length;i++) {
        if (pos[i] != i) return false;
    }
    return true;
}

function Puzzle(puzzle, mn, ii ,pathplugin) {
    //this.callback = callback;
    var w = measure(puzzle, "width");
	//alert(w);
    var h = measure(puzzle, "height");
    //alert(h);
    var n = Math.round(Math.sqrt(mn*h / w));
    if (n<2) n = 2;
    var m = Math.round(mn/n);
    if (m<2) m = 2;
    var N = m*n;
    this.m = m;
    this.n = n;

    var pos, missing;
    function parity2(k0, k1) {
        var j0 = k0 % m;
        var i0 = (k0-j0) / m;
        var j1 = k1 % m;
        var i1 = (k1-j1) / m;
        return (i1-i0+j1-j0)%2 ? -1 : 1;
    }
    while (true) {
        pos = random_permutation(N);
        if (is_identity(pos)) continue;
        //for (var i=1; i<N; ++i) {
        // if (pos[i]==N-1) missing=i;     
		// }
		missing=N-1;
        if (parity(pos) == parity2(missing, pos[N-1])) break;
    }
    this.missing = missing;
    this.pos = pos;

    this.dx = Math.floor((w-2)/m);
    this.dy = Math.floor((h-2)/n);
    var x0 = Math.floor((w - m*this.dx)/2);
    var y0 = Math.floor((h - n*this.dy)/2);

    var xbase = puzzle.offsetLeft + measure(puzzle, "blw", "pl");
    var ybase = puzzle.offsetTop + measure(puzzle, "btw", "pt");

    this.src = puzzle.src;

    this.canvas = document.createElement("div");
    this.canvas.className = "canvas";
    this.canvas.style.left = xbase+"px";
    this.canvas.style.top = ybase+"px";
    this.canvas.style.width = w+"px";
    this.canvas.style.height = h+"px";
    if (document.getElementById("backcolor"+ii).innerHTML!='')
	this.canvas.style.backgroundColor = document.getElementById("backcolor"+ii).innerHTML;
	//this.canvas.style.position= "absolute";
	//this.canvas.style.overflow = "hidden";
	//this.canvas.style.margin="0px";
	//this.canvas.style.border="none";
	//this.canvas.style.padding: "0px";
    puzzle.parentNode.appendChild(this.canvas);
    this.canvas.onmousedown = function(e) {
        // avoid selecting tiles with the mouse by mistake
        e.stopPropagation();
        e.preventDefault();
    };

    for (var k=0; k<N; ++k) {
        if (k == this.missing) continue;

        var tile = document.createElement('img');
        tile.className = "tile";
        tile.id = "t"+ii+"-"+k;
        tile.style.width = w+"px";
        tile.style.height = h+"px";
        tile.src = this.src;
        var j = k%this.m;
        var i = (k-j)/this.m;
        var l = x0+j*this.dx;
        var r = x0+(j+1)*this.dx-2;
        var t = y0+i*this.dy;
        var b = y0+(i+1)*this.dy-2;
        tile.style.clip = "rect("+t+"px,"+r+"px,"+b+"px,"+l+"px)";
		// tile.style.position= "absolute";
		// tile.style.margin="0px";
		// tile.style.border="none";
		// tile.style.padding: "0px";
        this.canvas.appendChild(tile);

        this.place_tile(k, pos[k], false,ii );
    }
    this.arm(ii,pathplugin);
}
Puzzle.prototype.place_tile = function(k, pos, slide_flag,ii) {
    var tile = document.getElementById("t"+ii+"-"+k);
    var j0 = k%this.m;
    var i0 = (k-j0)/this.m;
    var j1 = pos%this.m;
    var i1 = (pos-j1)/this.m;
    if (slide_flag) {
        slide_elem(tile, (j1-j0)*this.dx, (i1-i0)*this.dy);
    } else {
        tile.style.left = (j1-j0)*this.dx+"px";
        tile.style.top = (i1-i0)*this.dy+"px";
    };
}
Puzzle.prototype.move = function(k,ii,pathplugin) {
    var tmp = this.pos[this.missing];
    this.pos[this.missing] = this.pos[k];
    this.pos[k] = tmp;
    this.place_tile(k, tmp, true,ii);
    if (is_identity(this.pos)) {
        this.disarm(ii);
    } else {
        this.arm(ii,pathplugin);
    }
};
Puzzle.prototype.arm = function(ii,pathplugin) {
    var m = this.m;
    var N = this.m*this.n;
    var pos0 = this.pos[this.missing];
    var j0 = pos0 % m;
    var i0 = (pos0-j0) / m;
    for (var k=0; k<N; ++k) {
        if (k == this.missing) continue;

        var tile = document.getElementById("t"+ii+"-"+k);
        var pos = this.pos[k];
        var j = pos % m;
        var i = (pos-j) / m;
        var obj = this;
        var cb = (function(k) {
            return function(e) { obj.move(k,ii,pathplugin) };
        })(k);

        if (i == i0 && j+1 == j0) {
            tile.style.cursor = "url("+ pathplugin +"img/slide-puzzle-curright.png) 30 16, pointer";
            tile.onclick = cb;
        } else if (i+1 == i0 && j == j0) {
            tile.style.cursor = "url("+ pathplugin +"img/slide-puzzle-curdown.png) 18 30, pointer";
            tile.onclick = cb;
        } else if (i == i0 && j-1 == j0) {
            tile.style.cursor = "url("+ pathplugin +"img/slide-puzzle-curleft.png) 2 17, pointer";
            tile.onclick = cb;
        } else if (i-1 == i0 && j == j0) {
            tile.style.cursor = "url("+ pathplugin +"img/slide-puzzle-curup.png) 17 2, pointer";
            tile.onclick = cb;
        } else {
            tile.style.cursor = "auto";
            tile.onclick = null;
        }
    }
};
Puzzle.prototype.disarm = function(ii) {
    for (var k=0; k<this.N; ++k) {
        if (k == this.missing) continue;
        var tile = document.getElementById("t"+ii+"-"+k);
        tile.style.cursor = "auto";
        tile.onclick = null;
    }
    this.fade(ii);
};
Puzzle.prototype.set_alpha = function(a) {
    this.fade_count = a;
    if (a > 0) {
        this.canvas.style.background = "rgba(96,0,0,"+a*0.05+")";
        this.canvas.style.display = "block";
    } else {
        this.canvas.style.display = "none";
    }
};
Puzzle.prototype.do_fade = function(ii) {
    this.set_alpha(this.fade_count-1);
    if (this.fade_count > 0) {
        var obj = this;
        this.fade_id = setTimeout(function() { obj.do_fade(ii); }, 50);
    } else {
        this.canvas.parentNode.removeChild(this.canvas);
        obj = this;
        //game_index += 1;
        //localStorage.game_index = game_index+"";
		aa=ii+1;
		pp=this.pos.length;
		txt="sp_x_"+aa+"-"+pp;
        if (document.getElementById("endimage"+ii).src !='/')				{					document.getElementById(txt).src = document.getElementById("endimage"+ii).src ;					}				document.getElementById(txt).style.visibility = "visible";		
		if (document.getElementById("message"+ii).innerHTML !='')				{					alert(document.getElementById("message"+ii).innerHTML);					}
		
	
    }
};
Puzzle.prototype.fade = function(ii) {
    this.set_alpha(20);
    var obj = this;
    this.fade_id = setTimeout(function() { obj.do_fade(ii); }, 50);    //this.fade_id = obj.do_fade();
	//alert("Fantastic! you win! (www.colome.org)");
};

var game_index;

function set_screen(pathplugin) {
    //if (! name) name = "#title";
    // if (! name) name = "#play";
    // // new screen
    // screens = document.getElementsByTagName("div");
    // for (var i=0; i<screens.length; ++i) {
        // if (name == "#"+screens[i].id) {
            // screens[i].style.display = "block";
        // } else {
            // screens[i].style.display = "none";
        // }
    // }
    // if (name == "#play") {
        // cc = document.getElementsByClassName("canvas");
        // for (var i=0; i<cc.length; ++i) {
            // cc[i].parentNode.removeChild(cc[i]);
        // }
        var change = (function() {
            //var puzzle = document.getElementById("puzzle");
            return function(puzzle,i,pathplugin) {
                // inici modificat jordi
				var mn=12;
				var mn = puzzle.id;
				mn=mn.substring(mn.lastIndexOf('-')+1);
				//var mn = getUrlVars()["p"];
				if (mn==null) mn=12;
				var next = puzzle.src;
				//var next;
				//var next = getUrlVars()["img"];
				if (next==null) next="img002.jpg";
				
                function start() {
                    var level = game_index;
                    var image = next;
                    // level starts
                    new Puzzle(puzzle, mn,i,pathplugin);
                }
                // if (puzzle.src == next) {
                     start();
                // } else {
                    // puzzle.src = next;
                    // puzzle.onload = start;
                // }
            }
        })();
		cc=document.getElementsByName("sp_x");
        for (var i=0; i<cc.length; ++i) {
            change(cc[i],i,pathplugin);
        }
        //change();
    //}
}

check_hash = (function() {
    var hash;
    return function() {
        if (window.location.hash != hash) {
            hash = window.location.hash;
            set_screen(hash);
        }
    }
})();

function init() {
  sound = document.getElementById("sound");

//  if (localStorage.game_index == undefined) {
      game_index = 0;
//  } else {
//    game_index = parseInt(localStorage.game_index);
//  }

    setInterval(check_hash, 100);
}