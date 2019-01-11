/*------------------------------------------------------------------------

# mod_vsir - Very Simple Image Rotator

# ------------------------------------------------------------------------

# author    Joomla!Vargas

# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://joomla.vargas.co.cr

# Technical Support:  Forum - http://joomla.vargas.co.cr/forum

-------------------------------------------------------------------------*/



var Vsir = new Class({

    Implements: [Options, Events],

    options: {

        slideInterval: 3000,

        transitionDuration: 2000,

        startIndex: 0,

        autoplay: true

    },

    initialize: function (B, A) {

        this.setOptions(A);

        this.slides = $$(B);

        this.createFx();

        this.showSlide(this.options.startIndex);

        if (this.slides.length < 2) {

            this.options.autoplay = false

        }

        if (this.options.autoplay) {

            this.autoplay()

        }

        return this

    },

    toElement: function () {

        return this.container

    },

    createFx: function () {

        if (!this.slideFx) {

            this.slideFx = new Fx.Elements(this.slides, {

                duration: this.options.transitionDuration

            })

        }

        this.slides.each(function (A) {

            A.setStyle("opacity", 0)

            A.setStyle("z-index", 1)

        })

    },

    showSlide: function (B) {

        var A = {};

        this.slides.each(function (C, D) {

            if (D == B && D != this.currentSlide) {

                A[D.toString()] = {

                    "opacity": 1,

					"z-index": 1

                }

            } else {

                A[D.toString()] = {

                    "opacity": 0,

					"z-index": 0

                }

            }

        }, this);

        this.fireEvent("onShowSlide", B);

        this.currentSlide = B;

        this.slideFx.start(A);

        return this

    },

    autoplay: function () {

        this.slideshowInt = this.rotate.periodical(this.options.slideInterval, this);

        this.fireEvent("onAutoPlay");

        return this

    },

    stop: function () {

        $clear(this.slideshowInt);

        this.fireEvent("onStop");

        return this

    },

    rotate: function () {

        current = this.currentSlide;

        next = (current + 1 >= this.slides.length) ? 0 : current + 1;

        this.showSlide(next);

        this.fireEvent("onRotate", next);

        return this

    }

});



if ( typeof(Fx.Elements) !== "function" ) {



	Fx.Elements = new Class({

	

		Extends: Fx.CSS,

	

		initialize: function(elements, options){

			this.elements = this.subject = $$(elements);

			this.parent(options);

		},

	

		compute: function(from, to, delta){

			var now = {};

	

			for (var i in from){

				var iFrom = from[i], iTo = to[i], iNow = now[i] = {};

				for (var p in iFrom) iNow[p] = this.parent(iFrom[p], iTo[p], delta);

			}

	

			return now;

		},

	

		set: function(now){

			for (var i in now){

				if (!this.elements[i]) continue;

	

				var iNow = now[i];

				for (var p in iNow) this.render(this.elements[i], p, iNow[p], this.options.unit);

			}

	

			return this;

		},

	

		start: function(obj){

			if (!this.check(obj)) return this;

			var from = {}, to = {};

	

			for (var i in obj){

				if (!this.elements[i]) continue;

	

				var iProps = obj[i], iFrom = from[i] = {}, iTo = to[i] = {};

	

				for (var p in iProps){

					var parsed = this.prepare(this.elements[i], p, iProps[p]);

					iFrom[p] = parsed.from;

					iTo[p] = parsed.to;

				}

			}

	

			return this.parent(from, to);

		}

	

	});

}

	

