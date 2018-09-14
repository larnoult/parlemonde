/*
* Async Google Maps API loading
* Last commit: thx eslint, Mar 11, 2016 (https://github.com/shramov/leaflet-plugins/commits/master/layer/Layer.Deferred.js)
*/
L.DeferredLayer = L.LayerGroup.extend({
	options: {
		js: [],
		init: null
	},

	_script_cache: {},

	initialize: function (options) {
		L.Util.setOptions(this, options);
		L.LayerGroup.prototype.initialize.apply(this);
		this._loaded = false;
	},

	onAdd: function (map) {
		L.LayerGroup.prototype.onAdd.apply(this, [map]);
		if (this._loaded) return;
		var loaded = function () {
			this._loaded = true;
			var l = this.options.init();
			if (l)
				this.addLayer(l);
		};
		this._loadScripts(this.options.js.reverse(), L.Util.bind(loaded, this));
	},

	_loadScripts: function (scripts, cb, args) {
		if (!scripts || scripts.length === 0)
			return cb(args);
		var _this = this, s = scripts.pop(), c;
		c = this._script_cache[s];
		if (c === undefined) {
			c = {url: s, wait: []};
			var script = document.createElement('script');
			script.src = s;
			script.type = 'text/javascript';
			script.onload = function () {
				c.e.readyState = 'completed';
				var i = 0;
				for (i = 0; i < c.wait.length; i++)
					c.wait[i]();
			};
			c.e = script;
			document.getElementsByTagName('head')[0].appendChild(script);
		}
		function _cb () { _this._loadScripts(scripts, cb, args); }
		c.wait.push(_cb);
		if (c.e.readyState === 'completed')
			_cb();
		this._script_cache[s] = c;
	}
});

/*!
 * @overview es6-promise - a tiny implementation of Promises/A+.
 * @copyright Copyright (c) 2014 Yehuda Katz, Tom Dale, Stefan Penner and contributors (Conversion to ES6 API by Jake Archibald)
 * @license   Licensed under MIT license
 *            See https://raw.githubusercontent.com/stefanpenner/es6-promise/master/LICENSE
 * @version   4.1.0+f9a5575b (https://github.com/stefanpenner/es6-promise/releases)
 */
if (typeof Promise == 'undefined') { //only run if not available
	if (mapsmarkerjspro.google_maps_plugin == 'google_mutant') {
		(function (global, factory) {
			typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
			typeof define === 'function' && define.amd ? define(factory) :
			(global.ES6Promise = factory());
		}(this, (function () { 'use strict';

		function objectOrFunction(x) {
		  return typeof x === 'function' || typeof x === 'object' && x !== null;
		}

		function isFunction(x) {
		  return typeof x === 'function';
		}

		var _isArray = undefined;
		if (!Array.isArray) {
		  _isArray = function (x) {
			return Object.prototype.toString.call(x) === '[object Array]';
		  };
		} else {
		  _isArray = Array.isArray;
		}

		var isArray = _isArray;

		var len = 0;
		var vertxNext = undefined;
		var customSchedulerFn = undefined;

		var asap = function asap(callback, arg) {
		  queue[len] = callback;
		  queue[len + 1] = arg;
		  len += 2;
		  if (len === 2) {
			// If len is 2, that means that we need to schedule an async flush.
			// If additional callbacks are queued before the queue is flushed, they
			// will be processed by this flush that we are scheduling.
			if (customSchedulerFn) {
			  customSchedulerFn(flush);
			} else {
			  scheduleFlush();
			}
		  }
		};

		function setScheduler(scheduleFn) {
		  customSchedulerFn = scheduleFn;
		}

		function setAsap(asapFn) {
		  asap = asapFn;
		}

		var browserWindow = typeof window !== 'undefined' ? window : undefined;
		var browserGlobal = browserWindow || {};
		var BrowserMutationObserver = browserGlobal.MutationObserver || browserGlobal.WebKitMutationObserver;
		var isNode = typeof self === 'undefined' && typeof process !== 'undefined' && ({}).toString.call(process) === '[object process]';

		// test for web worker but not in IE10
		var isWorker = typeof Uint8ClampedArray !== 'undefined' && typeof importScripts !== 'undefined' && typeof MessageChannel !== 'undefined';

		// node
		function useNextTick() {
		  // node version 0.10.x displays a deprecation warning when nextTick is used recursively
		  // see https://github.com/cujojs/when/issues/410 for details
		  return function () {
			return process.nextTick(flush);
		  };
		}

		// vertx
		function useVertxTimer() {
		  if (typeof vertxNext !== 'undefined') {
			return function () {
			  vertxNext(flush);
			};
		  }

		  return useSetTimeout();
		}

		function useMutationObserver() {
		  var iterations = 0;
		  var observer = new BrowserMutationObserver(flush);
		  var node = document.createTextNode('');
		  observer.observe(node, { characterData: true });

		  return function () {
			node.data = iterations = ++iterations % 2;
		  };
		}

		// web worker
		function useMessageChannel() {
		  var channel = new MessageChannel();
		  channel.port1.onmessage = flush;
		  return function () {
			return channel.port2.postMessage(0);
		  };
		}

		function useSetTimeout() {
		  // Store setTimeout reference so es6-promise will be unaffected by
		  // other code modifying setTimeout (like sinon.useFakeTimers())
		  var globalSetTimeout = setTimeout;
		  return function () {
			return globalSetTimeout(flush, 1);
		  };
		}

		var queue = new Array(1000);
		function flush() {
		  for (var i = 0; i < len; i += 2) {
			var callback = queue[i];
			var arg = queue[i + 1];

			callback(arg);

			queue[i] = undefined;
			queue[i + 1] = undefined;
		  }

		  len = 0;
		}

		function attemptVertx() {
		  try {
			var r = require;
			var vertx = r('vertx');
			vertxNext = vertx.runOnLoop || vertx.runOnContext;
			return useVertxTimer();
		  } catch (e) {
			return useSetTimeout();
		  }
		}

		var scheduleFlush = undefined;
		// Decide what async method to use to triggering processing of queued callbacks:
		if (isNode) {
		  scheduleFlush = useNextTick();
		} else if (BrowserMutationObserver) {
		  scheduleFlush = useMutationObserver();
		} else if (isWorker) {
		  scheduleFlush = useMessageChannel();
		} else if (browserWindow === undefined && typeof require === 'function') {
		  scheduleFlush = attemptVertx();
		} else {
		  scheduleFlush = useSetTimeout();
		}

		function then(onFulfillment, onRejection) {
		  var _arguments = arguments;

		  var parent = this;

		  var child = new this.constructor(noop);

		  if (child[PROMISE_ID] === undefined) {
			makePromise(child);
		  }

		  var _state = parent._state;

		  if (_state) {
			(function () {
			  var callback = _arguments[_state - 1];
			  asap(function () {
				return invokeCallback(_state, child, callback, parent._result);
			  });
			})();
		  } else {
			subscribe(parent, child, onFulfillment, onRejection);
		  }

		  return child;
		}

		/**
		  `Promise.resolve` returns a promise that will become resolved with the
		  passed `value`. It is shorthand for the following:

		  ```javascript
		  let promise = new Promise(function(resolve, reject){
			resolve(1);
		  });

		  promise.then(function(value){
			// value === 1
		  });
		  ```

		  Instead of writing the above, your code now simply becomes the following:

		  ```javascript
		  let promise = Promise.resolve(1);

		  promise.then(function(value){
			// value === 1
		  });
		  ```

		  @method resolve
		  @static
		  @param {Any} value value that the returned promise will be resolved with
		  Useful for tooling.
		  @return {Promise} a promise that will become fulfilled with the given
		  `value`
		*/
		function resolve(object) {
		  /*jshint validthis:true */
		  var Constructor = this;

		  if (object && typeof object === 'object' && object.constructor === Constructor) {
			return object;
		  }

		  var promise = new Constructor(noop);
		  _resolve(promise, object);
		  return promise;
		}

		var PROMISE_ID = Math.random().toString(36).substring(16);

		function noop() {}

		var PENDING = void 0;
		var FULFILLED = 1;
		var REJECTED = 2;

		var GET_THEN_ERROR = new ErrorObject();

		function selfFulfillment() {
		  return new TypeError("You cannot resolve a promise with itself");
		}

		function cannotReturnOwn() {
		  return new TypeError('A promises callback cannot return that same promise.');
		}

		function getThen(promise) {
		  try {
			return promise.then;
		  } catch (error) {
			GET_THEN_ERROR.error = error;
			return GET_THEN_ERROR;
		  }
		}

		function tryThen(then, value, fulfillmentHandler, rejectionHandler) {
		  try {
			then.call(value, fulfillmentHandler, rejectionHandler);
		  } catch (e) {
			return e;
		  }
		}

		function handleForeignThenable(promise, thenable, then) {
		  asap(function (promise) {
			var sealed = false;
			var error = tryThen(then, thenable, function (value) {
			  if (sealed) {
				return;
			  }
			  sealed = true;
			  if (thenable !== value) {
				_resolve(promise, value);
			  } else {
				fulfill(promise, value);
			  }
			}, function (reason) {
			  if (sealed) {
				return;
			  }
			  sealed = true;

			  _reject(promise, reason);
			}, 'Settle: ' + (promise._label || ' unknown promise'));

			if (!sealed && error) {
			  sealed = true;
			  _reject(promise, error);
			}
		  }, promise);
		}

		function handleOwnThenable(promise, thenable) {
		  if (thenable._state === FULFILLED) {
			fulfill(promise, thenable._result);
		  } else if (thenable._state === REJECTED) {
			_reject(promise, thenable._result);
		  } else {
			subscribe(thenable, undefined, function (value) {
			  return _resolve(promise, value);
			}, function (reason) {
			  return _reject(promise, reason);
			});
		  }
		}

		function handleMaybeThenable(promise, maybeThenable, then$$) {
		  if (maybeThenable.constructor === promise.constructor && then$$ === then && maybeThenable.constructor.resolve === resolve) {
			handleOwnThenable(promise, maybeThenable);
		  } else {
			if (then$$ === GET_THEN_ERROR) {
			  _reject(promise, GET_THEN_ERROR.error);
			  GET_THEN_ERROR.error = null;
			} else if (then$$ === undefined) {
			  fulfill(promise, maybeThenable);
			} else if (isFunction(then$$)) {

			  handleForeignThenable(promise, maybeThenable, then$$);
			} else {
			  fulfill(promise, maybeThenable);
			}
		  }
		}

		function _resolve(promise, value) {
		  if (promise === value) {
			_reject(promise, selfFulfillment());
		  } else if (objectOrFunction(value)) {
			handleMaybeThenable(promise, value, getThen(value));
		  } else {
			fulfill(promise, value);
		  }
		}

		function publishRejection(promise) {
		  if (promise._onerror) {
			promise._onerror(promise._result);
		  }

		  publish(promise);
		}

		function fulfill(promise, value) {
		  if (promise._state !== PENDING) {
			return;
		  }

		  promise._result = value;
		  promise._state = FULFILLED;

		  if (promise._subscribers.length !== 0) {
			asap(publish, promise);
		  }
		}

		function _reject(promise, reason) {
		  if (promise._state !== PENDING) {
			return;
		  }
		  promise._state = REJECTED;
		  promise._result = reason;

		  asap(publishRejection, promise);
		}

		function subscribe(parent, child, onFulfillment, onRejection) {
		  var _subscribers = parent._subscribers;
		  var length = _subscribers.length;

		  parent._onerror = null;

		  _subscribers[length] = child;
		  _subscribers[length + FULFILLED] = onFulfillment;
		  _subscribers[length + REJECTED] = onRejection;

		  if (length === 0 && parent._state) {
			asap(publish, parent);
		  }
		}

		function publish(promise) {
		  var subscribers = promise._subscribers;
		  var settled = promise._state;

		  if (subscribers.length === 0) {
			return;
		  }

		  var child = undefined,
			  callback = undefined,
			  detail = promise._result;

		  for (var i = 0; i < subscribers.length; i += 3) {
			child = subscribers[i];
			callback = subscribers[i + settled];

			if (child) {
			  invokeCallback(settled, child, callback, detail);
			} else {
			  callback(detail);
			}
		  }

		  promise._subscribers.length = 0;
		}

		function ErrorObject() {
		  this.error = null;
		}

		var TRY_CATCH_ERROR = new ErrorObject();

		function tryCatch(callback, detail) {
		  try {
			return callback(detail);
		  } catch (e) {
			TRY_CATCH_ERROR.error = e;
			return TRY_CATCH_ERROR;
		  }
		}

		function invokeCallback(settled, promise, callback, detail) {
		  var hasCallback = isFunction(callback),
			  value = undefined,
			  error = undefined,
			  succeeded = undefined,
			  failed = undefined;

		  if (hasCallback) {
			value = tryCatch(callback, detail);

			if (value === TRY_CATCH_ERROR) {
			  failed = true;
			  error = value.error;
			  value.error = null;
			} else {
			  succeeded = true;
			}

			if (promise === value) {
			  _reject(promise, cannotReturnOwn());
			  return;
			}
		  } else {
			value = detail;
			succeeded = true;
		  }

		  if (promise._state !== PENDING) {
			// noop
		  } else if (hasCallback && succeeded) {
			  _resolve(promise, value);
			} else if (failed) {
			  _reject(promise, error);
			} else if (settled === FULFILLED) {
			  fulfill(promise, value);
			} else if (settled === REJECTED) {
			  _reject(promise, value);
			}
		}

		function initializePromise(promise, resolver) {
		  try {
			resolver(function resolvePromise(value) {
			  _resolve(promise, value);
			}, function rejectPromise(reason) {
			  _reject(promise, reason);
			});
		  } catch (e) {
			_reject(promise, e);
		  }
		}

		var id = 0;
		function nextId() {
		  return id++;
		}

		function makePromise(promise) {
		  promise[PROMISE_ID] = id++;
		  promise._state = undefined;
		  promise._result = undefined;
		  promise._subscribers = [];
		}

		function Enumerator(Constructor, input) {
		  this._instanceConstructor = Constructor;
		  this.promise = new Constructor(noop);

		  if (!this.promise[PROMISE_ID]) {
			makePromise(this.promise);
		  }

		  if (isArray(input)) {
			this._input = input;
			this.length = input.length;
			this._remaining = input.length;

			this._result = new Array(this.length);

			if (this.length === 0) {
			  fulfill(this.promise, this._result);
			} else {
			  this.length = this.length || 0;
			  this._enumerate();
			  if (this._remaining === 0) {
				fulfill(this.promise, this._result);
			  }
			}
		  } else {
			_reject(this.promise, validationError());
		  }
		}

		function validationError() {
		  return new Error('Array Methods must be provided an Array');
		};

		Enumerator.prototype._enumerate = function () {
		  var length = this.length;
		  var _input = this._input;

		  for (var i = 0; this._state === PENDING && i < length; i++) {
			this._eachEntry(_input[i], i);
		  }
		};

		Enumerator.prototype._eachEntry = function (entry, i) {
		  var c = this._instanceConstructor;
		  var resolve$$ = c.resolve;

		  if (resolve$$ === resolve) {
			var _then = getThen(entry);

			if (_then === then && entry._state !== PENDING) {
			  this._settledAt(entry._state, i, entry._result);
			} else if (typeof _then !== 'function') {
			  this._remaining--;
			  this._result[i] = entry;
			} else if (c === Promise) {
			  var promise = new c(noop);
			  handleMaybeThenable(promise, entry, _then);
			  this._willSettleAt(promise, i);
			} else {
			  this._willSettleAt(new c(function (resolve$$) {
				return resolve$$(entry);
			  }), i);
			}
		  } else {
			this._willSettleAt(resolve$$(entry), i);
		  }
		};

		Enumerator.prototype._settledAt = function (state, i, value) {
		  var promise = this.promise;

		  if (promise._state === PENDING) {
			this._remaining--;

			if (state === REJECTED) {
			  _reject(promise, value);
			} else {
			  this._result[i] = value;
			}
		  }

		  if (this._remaining === 0) {
			fulfill(promise, this._result);
		  }
		};

		Enumerator.prototype._willSettleAt = function (promise, i) {
		  var enumerator = this;

		  subscribe(promise, undefined, function (value) {
			return enumerator._settledAt(FULFILLED, i, value);
		  }, function (reason) {
			return enumerator._settledAt(REJECTED, i, reason);
		  });
		};

		/**
		  `Promise.all` accepts an array of promises, and returns a new promise which
		  is fulfilled with an array of fulfillment values for the passed promises, or
		  rejected with the reason of the first passed promise to be rejected. It casts all
		  elements of the passed iterable to promises as it runs this algorithm.

		  Example:

		  ```javascript
		  let promise1 = resolve(1);
		  let promise2 = resolve(2);
		  let promise3 = resolve(3);
		  let promises = [ promise1, promise2, promise3 ];

		  Promise.all(promises).then(function(array){
			// The array here would be [ 1, 2, 3 ];
		  });
		  ```

		  If any of the `promises` given to `all` are rejected, the first promise
		  that is rejected will be given as an argument to the returned promises's
		  rejection handler. For example:

		  Example:

		  ```javascript
		  let promise1 = resolve(1);
		  let promise2 = reject(new Error("2"));
		  let promise3 = reject(new Error("3"));
		  let promises = [ promise1, promise2, promise3 ];

		  Promise.all(promises).then(function(array){
			// Code here never runs because there are rejected promises!
		  }, function(error) {
			// error.message === "2"
		  });
		  ```

		  @method all
		  @static
		  @param {Array} entries array of promises
		  @param {String} label optional string for labeling the promise.
		  Useful for tooling.
		  @return {Promise} promise that is fulfilled when all `promises` have been
		  fulfilled, or rejected if any of them become rejected.
		  @static
		*/
		function all(entries) {
		  return new Enumerator(this, entries).promise;
		}

		/**
		  `Promise.race` returns a new promise which is settled in the same way as the
		  first passed promise to settle.

		  Example:

		  ```javascript
		  let promise1 = new Promise(function(resolve, reject){
			setTimeout(function(){
			  resolve('promise 1');
			}, 200);
		  });

		  let promise2 = new Promise(function(resolve, reject){
			setTimeout(function(){
			  resolve('promise 2');
			}, 100);
		  });

		  Promise.race([promise1, promise2]).then(function(result){
			// result === 'promise 2' because it was resolved before promise1
			// was resolved.
		  });
		  ```

		  `Promise.race` is deterministic in that only the state of the first
		  settled promise matters. For example, even if other promises given to the
		  `promises` array argument are resolved, but the first settled promise has
		  become rejected before the other promises became fulfilled, the returned
		  promise will become rejected:

		  ```javascript
		  let promise1 = new Promise(function(resolve, reject){
			setTimeout(function(){
			  resolve('promise 1');
			}, 200);
		  });

		  let promise2 = new Promise(function(resolve, reject){
			setTimeout(function(){
			  reject(new Error('promise 2'));
			}, 100);
		  });

		  Promise.race([promise1, promise2]).then(function(result){
			// Code here never runs
		  }, function(reason){
			// reason.message === 'promise 2' because promise 2 became rejected before
			// promise 1 became fulfilled
		  });
		  ```

		  An example real-world use case is implementing timeouts:

		  ```javascript
		  Promise.race([ajax('foo.json'), timeout(5000)])
		  ```

		  @method race
		  @static
		  @param {Array} promises array of promises to observe
		  Useful for tooling.
		  @return {Promise} a promise which settles in the same way as the first passed
		  promise to settle.
		*/
		function race(entries) {
		  /*jshint validthis:true */
		  var Constructor = this;

		  if (!isArray(entries)) {
			return new Constructor(function (_, reject) {
			  return reject(new TypeError('You must pass an array to race.'));
			});
		  } else {
			return new Constructor(function (resolve, reject) {
			  var length = entries.length;
			  for (var i = 0; i < length; i++) {
				Constructor.resolve(entries[i]).then(resolve, reject);
			  }
			});
		  }
		}

		/**
		  `Promise.reject` returns a promise rejected with the passed `reason`.
		  It is shorthand for the following:

		  ```javascript
		  let promise = new Promise(function(resolve, reject){
			reject(new Error('WHOOPS'));
		  });

		  promise.then(function(value){
			// Code here doesn't run because the promise is rejected!
		  }, function(reason){
			// reason.message === 'WHOOPS'
		  });
		  ```

		  Instead of writing the above, your code now simply becomes the following:

		  ```javascript
		  let promise = Promise.reject(new Error('WHOOPS'));

		  promise.then(function(value){
			// Code here doesn't run because the promise is rejected!
		  }, function(reason){
			// reason.message === 'WHOOPS'
		  });
		  ```

		  @method reject
		  @static
		  @param {Any} reason value that the returned promise will be rejected with.
		  Useful for tooling.
		  @return {Promise} a promise rejected with the given `reason`.
		*/
		function reject(reason) {
		  /*jshint validthis:true */
		  var Constructor = this;
		  var promise = new Constructor(noop);
		  _reject(promise, reason);
		  return promise;
		}

		function needsResolver() {
		  throw new TypeError('You must pass a resolver function as the first argument to the promise constructor');
		}

		function needsNew() {
		  throw new TypeError("Failed to construct 'Promise': Please use the 'new' operator, this object constructor cannot be called as a function.");
		}

		/**
		  Promise objects represent the eventual result of an asynchronous operation. The
		  primary way of interacting with a promise is through its `then` method, which
		  registers callbacks to receive either a promise's eventual value or the reason
		  why the promise cannot be fulfilled.

		  Terminology
		  -----------

		  - `promise` is an object or function with a `then` method whose behavior conforms to this specification.
		  - `thenable` is an object or function that defines a `then` method.
		  - `value` is any legal JavaScript value (including undefined, a thenable, or a promise).
		  - `exception` is a value that is thrown using the throw statement.
		  - `reason` is a value that indicates why a promise was rejected.
		  - `settled` the final resting state of a promise, fulfilled or rejected.

		  A promise can be in one of three states: pending, fulfilled, or rejected.

		  Promises that are fulfilled have a fulfillment value and are in the fulfilled
		  state.  Promises that are rejected have a rejection reason and are in the
		  rejected state.  A fulfillment value is never a thenable.

		  Promises can also be said to *resolve* a value.  If this value is also a
		  promise, then the original promise's settled state will match the value's
		  settled state.  So a promise that *resolves* a promise that rejects will
		  itself reject, and a promise that *resolves* a promise that fulfills will
		  itself fulfill.


		  Basic Usage:
		  ------------

		  ```js
		  let promise = new Promise(function(resolve, reject) {
			// on success
			resolve(value);

			// on failure
			reject(reason);
		  });

		  promise.then(function(value) {
			// on fulfillment
		  }, function(reason) {
			// on rejection
		  });
		  ```

		  Advanced Usage:
		  ---------------

		  Promises shine when abstracting away asynchronous interactions such as
		  `XMLHttpRequest`s.

		  ```js
		  function getJSON(url) {
			return new Promise(function(resolve, reject){
			  let xhr = new XMLHttpRequest();

			  xhr.open('GET', url);
			  xhr.onreadystatechange = handler;
			  xhr.responseType = 'json';
			  xhr.setRequestHeader('Accept', 'application/json');
			  xhr.send();

			  function handler() {
				if (this.readyState === this.DONE) {
				  if (this.status === 200) {
					resolve(this.response);
				  } else {
					reject(new Error('getJSON: `' + url + '` failed with status: [' + this.status + ']'));
				  }
				}
			  };
			});
		  }

		  getJSON('/posts.json').then(function(json) {
			// on fulfillment
		  }, function(reason) {
			// on rejection
		  });
		  ```

		  Unlike callbacks, promises are great composable primitives.

		  ```js
		  Promise.all([
			getJSON('/posts'),
			getJSON('/comments')
		  ]).then(function(values){
			values[0] // => postsJSON
			values[1] // => commentsJSON

			return values;
		  });
		  ```

		  @class Promise
		  @param {function} resolver
		  Useful for tooling.
		  @constructor
		*/
		function Promise(resolver) {
		  this[PROMISE_ID] = nextId();
		  this._result = this._state = undefined;
		  this._subscribers = [];

		  if (noop !== resolver) {
			typeof resolver !== 'function' && needsResolver();
			this instanceof Promise ? initializePromise(this, resolver) : needsNew();
		  }
		}

		Promise.all = all;
		Promise.race = race;
		Promise.resolve = resolve;
		Promise.reject = reject;
		Promise._setScheduler = setScheduler;
		Promise._setAsap = setAsap;
		Promise._asap = asap;

		Promise.prototype = {
		  constructor: Promise,

		  /**
			The primary way of interacting with a promise is through its `then` method,
			which registers callbacks to receive either a promise's eventual value or the
			reason why the promise cannot be fulfilled.

			```js
			findUser().then(function(user){
			  // user is available
			}, function(reason){
			  // user is unavailable, and you are given the reason why
			});
			```

			Chaining
			--------

			The return value of `then` is itself a promise.  This second, 'downstream'
			promise is resolved with the return value of the first promise's fulfillment
			or rejection handler, or rejected if the handler throws an exception.

			```js
			findUser().then(function (user) {
			  return user.name;
			}, function (reason) {
			  return 'default name';
			}).then(function (userName) {
			  // If `findUser` fulfilled, `userName` will be the user's name, otherwise it
			  // will be `'default name'`
			});

			findUser().then(function (user) {
			  throw new Error('Found user, but still unhappy');
			}, function (reason) {
			  throw new Error('`findUser` rejected and we're unhappy');
			}).then(function (value) {
			  // never reached
			}, function (reason) {
			  // if `findUser` fulfilled, `reason` will be 'Found user, but still unhappy'.
			  // If `findUser` rejected, `reason` will be '`findUser` rejected and we're unhappy'.
			});
			```
			If the downstream promise does not specify a rejection handler, rejection reasons will be propagated further downstream.

			```js
			findUser().then(function (user) {
			  throw new PedagogicalException('Upstream error');
			}).then(function (value) {
			  // never reached
			}).then(function (value) {
			  // never reached
			}, function (reason) {
			  // The `PedgagocialException` is propagated all the way down to here
			});
			```

			Assimilation
			------------

			Sometimes the value you want to propagate to a downstream promise can only be
			retrieved asynchronously. This can be achieved by returning a promise in the
			fulfillment or rejection handler. The downstream promise will then be pending
			until the returned promise is settled. This is called *assimilation*.

			```js
			findUser().then(function (user) {
			  return findCommentsByAuthor(user);
			}).then(function (comments) {
			  // The user's comments are now available
			});
			```

			If the assimliated promise rejects, then the downstream promise will also reject.

			```js
			findUser().then(function (user) {
			  return findCommentsByAuthor(user);
			}).then(function (comments) {
			  // If `findCommentsByAuthor` fulfills, we'll have the value here
			}, function (reason) {
			  // If `findCommentsByAuthor` rejects, we'll have the reason here
			});
			```

			Simple Example
			--------------

			Synchronous Example

			```javascript
			let result;

			try {
			  result = findResult();
			  // success
			} catch(reason) {
			  // failure
			}
			```

			Errback Example

			```js
			findResult(function(result, err){
			  if (err) {
				// failure
			  } else {
				// success
			  }
			});
			```

			Promise Example;

			```javascript
			findResult().then(function(result){
			  // success
			}, function(reason){
			  // failure
			});
			```

			Advanced Example
			--------------

			Synchronous Example

			```javascript
			let author, books;

			try {
			  author = findAuthor();
			  books  = findBooksByAuthor(author);
			  // success
			} catch(reason) {
			  // failure
			}
			```

			Errback Example

			```js

			function foundBooks(books) {

			}

			function failure(reason) {

			}

			findAuthor(function(author, err){
			  if (err) {
				failure(err);
				// failure
			  } else {
				try {
				  findBoooksByAuthor(author, function(books, err) {
					if (err) {
					  failure(err);
					} else {
					  try {
						foundBooks(books);
					  } catch(reason) {
						failure(reason);
					  }
					}
				  });
				} catch(error) {
				  failure(err);
				}
				// success
			  }
			});
			```

			Promise Example;

			```javascript
			findAuthor().
			  then(findBooksByAuthor).
			  then(function(books){
				// found books
			}).catch(function(reason){
			  // something went wrong
			});
			```

			@method then
			@param {Function} onFulfilled
			@param {Function} onRejected
			Useful for tooling.
			@return {Promise}
		  */
		  then: then,

		  /**
			`catch` is simply sugar for `then(undefined, onRejection)` which makes it the same
			as the catch block of a try/catch statement.

			```js
			function findAuthor(){
			  throw new Error('couldn't find that author');
			}

			// synchronous
			try {
			  findAuthor();
			} catch(reason) {
			  // something went wrong
			}

			// async with promises
			findAuthor().catch(function(reason){
			  // something went wrong
			});
			```

			@method catch
			@param {Function} onRejection
			Useful for tooling.
			@return {Promise}
		  */
		  'catch': function _catch(onRejection) {
			return this.then(null, onRejection);
		  }
		};

		function polyfill() {
			var local = undefined;

			if (typeof global !== 'undefined') {
				local = global;
			} else if (typeof self !== 'undefined') {
				local = self;
			} else {
				try {
					local = Function('return this')();
				} catch (e) {
					throw new Error('polyfill failed because global object is unavailable in this environment');
				}
			}

			var P = local.Promise;

			if (P) {
				var promiseToString = null;
				try {
					promiseToString = Object.prototype.toString.call(P.resolve());
				} catch (e) {
					// silently ignored
				}

				if (promiseToString === '[object Promise]' && !P.cast) {
					return;
				}
			}

			local.Promise = Promise;
		}

		// Strange compat..
		Promise.polyfill = polyfill;
		Promise.Promise = Promise;

		Promise.polyfill();

		return Promise;

		})));
		//# sourceMappingURL=es6-promise.auto.map
	}
}

/*
 * Google Maps plugins
 * GoogleMutant: https://gitlab.com/IvanSanchez/Leaflet.GridLayer.GoogleMutant
 * Legacy plugin: http://psha.org.ru/b/leaflet-plugins.html
 * Last commit: 24.5.2017 (https://gitlab.com/IvanSanchez/Leaflet.GridLayer.GoogleMutant/commits/master)
*/
if (mapsmarkerjspro.google_maps_api_status == 'enabled') {

	if (mapsmarkerjspro.google_maps_plugin == 'google_mutant') {

		if (mapsmarkerjspro.google_deferred_loading == 'disabled') {
			google.load('maps', '3', {'other_params':mapsmarkerjspro.googlemaps_language+mapsmarkerjspro.googlemaps_base_domain+'&key='+mapsmarkerjspro.google_maps_api_key});
		}

		//info: prepare custom Google Map styles
		if (mapsmarkerjspro.google_styling_json == 'disabled') {
			var custom_google_maps_styles = '';
		} else {
			var custom_google_maps_styles = eval(mapsmarkerjspro.google_styling_json);
		}

		// Based on https://github.com/shramov/leaflet-plugins
		// GridLayer like https://avinmathew.com/leaflet-and-google-maps/ , but using MutationObserver instead of jQuery


		// ðŸ‚class GridLayer.GoogleMutant
		// ðŸ‚extends GridLayer
		L.GridLayer.GoogleMutant = L.GridLayer.extend({
			includes: L.Mixin.Events,

			options: {
				minZoom: 0,
				maxZoom: mapsmarkerjspro.maxzoom,
				tileSize: 256,
				subdomains: 'abc',
				edgeBufferTiles: 0, //info: needs to be 0 see #385
				errorTileUrl: '',
				attribution: '',	// The mutant container will add its own attribution anyways.
				opacity: 1,
				continuousWorld: false,
				noWrap: false,
				// ðŸ‚option type: String = 'roadmap'
				// Google's map type. Valid values are 'roadmap', 'satellite' or 'terrain'. 'hybrid' is not really supported.
				type: 'roadmap',
				maxNativeZoom: 21,
				styles: custom_google_maps_styles
			},

			initialize: function (options) {
				L.GridLayer.prototype.initialize.call(this, options);

				this._ready = !!window.google && !!window.google.maps && !!window.google.maps.Map;

				this._GAPIPromise = this._ready ? Promise.resolve(window.google) : new Promise(function (resolve, reject) {
					var checkCounter = 0;
					var intervalId = null;
					intervalId = setInterval(function () {
						if (checkCounter >= 10) {
							clearInterval(intervalId);
							return reject(new Error('window.google not found after 10 attempts'));
						}
						if (!!window.google && !!window.google.maps && !!window.google.maps.Map) {
							clearInterval(intervalId);
							return resolve(window.google);
						}
						checkCounter++;
					}, 500);
				});

				// Couple data structures indexed by tile key
				this._tileCallbacks = {};	// Callbacks for promises for tiles that are expected
				this._freshTiles = {};	// Tiles from the mutant which haven't been requested yet

				this._imagesPerTile = (this.options.type === 'hybrid') ? 2 : 1;
			},

			onAdd: function (map) {
				L.GridLayer.prototype.onAdd.call(this, map);
				this._initMutantContainer();

				this._GAPIPromise.then(function () {
					this._ready = true;
					this._map = map;

					this._initMutant();

					map.on('viewreset', this._reset, this);
					map.on('move', this._update, this);
					map.on('zoomend', this._handleZoomAnim, this);
					map.on('resize', this._resize, this);

					//handle layer being added to a map for which there are no Google tiles at the given zoom
					google.maps.event.addListenerOnce(this._mutant, 'idle', function () {
						this._checkZoomLevels();
						this._mutantIsReady = true;
					}.bind(this));

					//20px instead of 1em to avoid a slight overlap with google's attribution
					map._controlCorners.bottomright.style.marginBottom = '14px';
					map._controlCorners.bottomleft.style.marginBottom = '21px';

					this._reset();
					this._update();

					if (this._subLayers) {
						//restore previously added google layers
						for (var layerName in this._subLayers) {
							this._subLayers[layerName].setMap(this._mutant);
						}
					}
				}.bind(this));
			},

			onRemove: function (map) {
				L.GridLayer.prototype.onRemove.call(this, map);
				map._container.removeChild(this._mutantContainer);
				this._mutantContainer = undefined;

				google.maps.event.clearListeners(map, 'idle');
				google.maps.event.clearListeners(this._mutant, 'idle');
				map.off('viewreset', this._reset, this);
				map.off('move', this._update, this);
				map.off('zoomend', this._handleZoomAnim, this);
				map.off('resize', this._resize, this);

				map._controlCorners.bottomright.style.marginBottom = '0em';
				map._controlCorners.bottomleft.style.marginBottom = '0em';
			},

			getAttribution: function () {
				return this.options.attribution;
			},

			setOpacity: function (opacity) {
				this.options.opacity = opacity;
				if (opacity < 1) {
					L.DomUtil.setOpacity(this._mutantContainer, opacity);
				}
			},

			setElementSize: function (e, size) {
				e.style.width = size.x + 'px';
				e.style.height = size.y + 'px';
			},


			addGoogleLayer: function (googleLayerName, options) {
				if (!this._subLayers) this._subLayers = {};
				return this._GAPIPromise.then(function () {
					var Constructor = google.maps[googleLayerName];
					var googleLayer = new Constructor(options);
					googleLayer.setMap(this._mutant);
					this._subLayers[googleLayerName] = googleLayer;
					return googleLayer;
				}.bind(this));
			},

			removeGoogleLayer: function (googleLayerName) {
				var googleLayer = this._subLayers && this._subLayers[googleLayerName];
				if (!googleLayer) return;

				googleLayer.setMap(null);
				delete this._subLayers[googleLayerName];
			},


			_initMutantContainer: function () {
				if (!this._mutantContainer) {
					this._mutantContainer = L.DomUtil.create('div', 'leaflet-google-mutant leaflet-top leaflet-left');
					this._mutantContainer.id = '_MutantContainer_' + L.Util.stamp(this._mutantContainer);
					this._mutantContainer.style.zIndex = '399'; //leaflet map pane at 400, controls at 1000, 399 needed for iOS
					this._mutantContainer.style.pointerEvents = 'none';

					this._map.getContainer().appendChild(this._mutantContainer);
				}

				this.setOpacity(this.options.opacity);
				this.setElementSize(this._mutantContainer, this._map.getSize());

				this._attachObserver(this._mutantContainer);
			},

			_initMutant: function () {
				if (!this._ready || !this._mutantContainer) return;
				this._mutantCenter = new google.maps.LatLng(0, 0);

				var map = new google.maps.Map(this._mutantContainer, {
					center: this._mutantCenter,
					zoom: 0,
					tilt: 0,
					mapTypeId: this.options.type,
					disableDefaultUI: true,
					keyboardShortcuts: false,
					draggable: false,
					disableDoubleClickZoom: true,
					scrollwheel: false,
					streetViewControl: false,
					styles: this.options.styles || {},
					backgroundColor: 'transparent'
				});

				this._mutant = map;

				google.maps.event.addListenerOnce(map, 'idle', function () {
					var nodes = this._mutantContainer.querySelectorAll('a');
					for (var i = 0; i < nodes.length; i++) {
						nodes[i].style.pointerEvents = 'auto';
					}
				}.bind(this));

				// ðŸ‚event spawned
				// Fired when the mutant has been created.
				this.fire('spawned', {mapObject: map});
			},

			_attachObserver: function _attachObserver (node) {
		// 		console.log('Gonna observe', node);

				var observer = new MutationObserver(this._onMutations.bind(this));

				// pass in the target node, as well as the observer options
				observer.observe(node, { childList: true, subtree: true });
			},

			_onMutations: function _onMutations (mutations) {
				for (var i = 0; i < mutations.length; ++i) {
					var mutation = mutations[i];
					for (var j = 0; j < mutation.addedNodes.length; ++j) {
						var node = mutation.addedNodes[j];

						if (node instanceof HTMLImageElement) {
							this._onMutatedImage(node);
						} else if (node instanceof HTMLElement) {
							Array.prototype.forEach.call(node.querySelectorAll('img'), this._onMutatedImage.bind(this));
						}
					}
				}
			},

			// Only images which 'src' attrib match this will be considered for moving around.
			// Looks like some kind of string-based protobuf, maybe??
			// Only the roads (and terrain, and vector-based stuff) match this pattern
			_roadRegexp: /!1i(\d+)!2i(\d+)!3i(\d+)!/,

			// On the other hand, raster imagery matches this other pattern
			_satRegexp: /x=(\d+)&y=(\d+)&z=(\d+)/,

			// On small viewports, when zooming in/out, a static image is requested
			// This will not be moved around, just removed from the DOM.
			_staticRegExp: /StaticMapService\.GetMapImage/,

			_onMutatedImage: function _onMutatedImage (imgNode) {
		// 		if (imgNode.src) {
		// 			console.log('caught mutated image: ', imgNode.src);
		// 		}

				var coords;
				var match = imgNode.src.match(this._roadRegexp);
				var sublayer = 0;

				if (match) {
					coords = {
						z: match[1],
						x: match[2],
						y: match[3]
					};
					if (this._imagesPerTile > 1) {
						imgNode.style.zIndex = 1;
						sublayer = 1;
					}
				} else {
					match = imgNode.src.match(this._satRegexp);
					if (match) {
						coords = {
							x: match[1],
							y: match[2],
							z: match[3]
						};
					}
		// 			imgNode.style.zIndex = 0;
					sublayer = 0;
				}

				if (coords) {
					var tileKey = this._tileCoordsToKey(coords);
					imgNode.style.position = 'absolute';
					imgNode.style.visibility = 'hidden';

					var key = tileKey + '/' + sublayer;
					// console.log('mutation for tile', key)
					//store img so it can also be used in subsequent tile requests
					this._freshTiles[key] = imgNode;

					if (key in this._tileCallbacks && this._tileCallbacks[key]) {
		// console.log('Fullfilling callback ', key);
						//fullfill most recent tileCallback because there maybe callbacks that will never get a
						//corresponding mutation (because map moved to quickly...)
						this._tileCallbacks[key].pop()(imgNode);
						if (!this._tileCallbacks[key].length) { delete this._tileCallbacks[key]; }
					} else {
						if (this._tiles[tileKey]) {
							//we already have a tile in this position (mutation is probably a google layer being added)
							//replace it
							var c = this._tiles[tileKey].el;
							var oldImg = (sublayer === 0) ? c.firstChild : c.firstChild.nextSibling;
							var cloneImgNode = this._clone(imgNode);
							c.replaceChild(cloneImgNode, oldImg);
						}
					}
				} else if (imgNode.src.match(this._staticRegExp)) {
					imgNode.style.visibility = 'hidden';
				}
			},


			createTile: function (coords, done) {
				var key = this._tileCoordsToKey(coords);

				var tileContainer = L.DomUtil.create('div');
				tileContainer.dataset.pending = this._imagesPerTile;
				done = done.bind(this, null, tileContainer);

				for (var i = 0; i < this._imagesPerTile; i++) {
					var key2 = key + '/' + i;
					if (key2 in this._freshTiles) {
						var imgNode = this._freshTiles[key2];
						tileContainer.appendChild(this._clone(imgNode));
						tileContainer.dataset.pending--;
		// 				console.log('Got ', key2, ' from _freshTiles');
					} else {
						this._tileCallbacks[key2] = this._tileCallbacks[key2] || [];
						this._tileCallbacks[key2].push( (function (c/*, k2*/) {
							return function (imgNode) {
								c.appendChild(this._clone(imgNode));
								c.dataset.pending--;
								if (!parseInt(c.dataset.pending)) { done(); }
		// 						console.log('Sent ', k2, ' to _tileCallbacks, still ', c.dataset.pending, ' images to go');
							}.bind(this);
						}.bind(this))(tileContainer/*, key2*/) );
					}
				}

				if (!parseInt(tileContainer.dataset.pending)) {
					L.Util.requestAnimFrame(done);
				}
				return tileContainer;
			},

			_clone: function (imgNode) {
				var clonedImgNode = imgNode.cloneNode(true);
				clonedImgNode.style.visibility = 'visible';
				return clonedImgNode;
			},

			_checkZoomLevels: function () {
				//setting the zoom level on the Google map may result in a different zoom level than the one requested
				//(it won't go beyond the level for which they have data).
				var zoomLevel = this._map.getZoom();
				var gMapZoomLevel = this._mutant.getZoom();
				if (!zoomLevel || !gMapZoomLevel) return;


				if ((gMapZoomLevel !== zoomLevel) || //zoom levels are out of sync, Google doesn't have data
					(gMapZoomLevel > this.options.maxNativeZoom)) { //at current location, Google does have data (contrary to maxNativeZoom)
					//Update maxNativeZoom
					this._setMaxNativeZoom(gMapZoomLevel);
				}
			},

			_setMaxNativeZoom: function (zoomLevel) {
				if (zoomLevel != this.options.maxNativeZoom) {
					this.options.maxNativeZoom = zoomLevel;
					this._resetView();
				}
			},

			_reset: function () {
				this._initContainer();
			},

			_update: function () {
				// zoom level check needs to happen before super's implementation (tile addition/creation)
				// otherwise tiles may be missed if maxNativeZoom is not yet correctly determined
				if (this._mutant) {
					var center = this._map.getCenter();
					var _center = new google.maps.LatLng(center.lat, center.lng);

					this._mutant.setCenter(_center);
					var zoom = this._map.getZoom();
					var fractionalLevel = zoom !== Math.round(zoom);
					var mutantZoom = this._mutant.getZoom();

					//ignore fractional zoom levels
					if (!fractionalLevel && (zoom != mutantZoom)) {
						this._mutant.setZoom(zoom);

						if (this._mutantIsReady) this._checkZoomLevels();
						//else zoom level check will be done later by 'idle' handler
					}
				}

				L.GridLayer.prototype._update.call(this);
			},

			_resize: function () {
				var size = this._map.getSize();
				if (this._mutantContainer.style.width === size.x &&
					this._mutantContainer.style.height === size.y)
					return;
				this.setElementSize(this._mutantContainer, size);
				if (!this._mutant) return;
				google.maps.event.trigger(this._mutant, 'resize');
			},

			_handleZoomAnim: function () {
				if (!this._mutant) return;
				var center = this._map.getCenter();
				var _center = new google.maps.LatLng(center.lat, center.lng);

				this._mutant.setCenter(_center);
				this._mutant.setZoom(Math.round(this._map.getZoom()));
			},

			// Agressively prune _freshtiles when a tile with the same key is removed,
			// this prevents a problem where Leaflet keeps a loaded tile longer than
			// GMaps, so that GMaps makes two requests but Leaflet only consumes one,
			// polluting _freshTiles with stale data.
			_removeTile: function (key) {
				if (!this._mutant) return;

				//give time for animations to finish before checking it tile should be pruned
				setTimeout(this._pruneTile.bind(this, key), 1000);


				return L.GridLayer.prototype._removeTile.call(this, key);
			},

			_pruneTile: function (key) {
				var gZoom = this._mutant.getZoom();
				var tileZoom = key.split(':')[2];
				var googleBounds = this._mutant.getBounds();
				var sw = googleBounds.getSouthWest();
				var ne = googleBounds.getNorthEast();
				var gMapBounds = L.latLngBounds([[sw.lat(), sw.lng()], [ne.lat(), ne.lng()]]);

				for (var i=0; i<this._imagesPerTile; i++) {
					var key2 = key + '/' + i;
					if (key2 in this._freshTiles) {
						var tileBounds = this._map && this._keyToBounds(key);
						var stillVisible = this._map && tileBounds.overlaps(gMapBounds) && (tileZoom == gZoom);

						if (!stillVisible) delete this._freshTiles[key2];
		//				console.log('Prunning of ', key, (!stillVisible))
					}
				}
			}
		});


		// ðŸ‚factory gridLayer.googleMutant(options)
		// Returns a new `GridLayer.GoogleMutant` given its options
		L.gridLayer.googleMutant = function (options) {
			return new L.GridLayer.GoogleMutant(options);
		};

	} else if (mapsmarkerjspro.google_maps_plugin == 'google_legacy') {

		if (mapsmarkerjspro.google_deferred_loading == 'disabled') {
			google.load('maps', '3', {'other_params':mapsmarkerjspro.googlemaps_language+mapsmarkerjspro.googlemaps_base_domain+'&key='+mapsmarkerjspro.google_maps_api_key});
		}

		L.Google = L.Layer.extend({
			includes: L.Mixin.Events,

			options: {
				minZoom: 0,
				maxZoom: mapsmarkerjspro.maxzoom,
				maxNativeZoom: 21,
				tileSize: 256,
				subdomains: 'abc',
				errorTileUrl: '',
				attribution: '',
				opacity: 1,
				continuousWorld: false,
				noWrap: false,
				mapOptions: {
					backgroundColor: '#F6F6F6'
				}
			},

			// Possible types: SATELLITE, ROADMAP, HYBRID, TERRAIN
			initialize: function (type, options) {
				L.Util.setOptions(this, options);

				this._ready = L.Google.isGoogleMapsReady();
				if (!this._ready) L.Google.asyncWait.push(this);

				this._type = type || 'SATELLITE';
			},

			onAdd: function (map, insertAtTheBottom) {
				this._map = map;
				this._insertAtTheBottom = insertAtTheBottom;

				// create a container div for tiles
				this._initContainer();
				this._initMapObject();

				// set up events
				map.on('viewreset', this._reset, this);

				this._limitedUpdate = L.Util.throttle(this._update, 0, this);
				map.on('move drag', this._update, this);

				map.on('zoomanim', this._handleZoomAnim, this);

				map._controlCorners['bottomright'].style.marginBottom = '15px';
				map._controlCorners['bottomleft'].style.marginBottom = '21px';

				this._reset();
				this._update();
				L.polyline([[0, 0]]).addTo(this._map); //info: temp fix for https://github.com/shramov/leaflet-plugins/issues/156 - check with new leaflet-version!
			},

			onRemove: function (map) {
				map._container.removeChild(this._container);

				map.off('viewreset', this._reset, this);

				map.off('move drag', this._update, this);

				map.off('zoomanim', this._handleZoomAnim, this);

				map._controlCorners.bottomright.style.marginBottom = '0em';
			},

			getAttribution: function () {
				return this.options.attribution;
			},

			setOpacity: function (opacity) {
				this.options.opacity = opacity;
				if (opacity < 1) {
					L.DomUtil.setOpacity(this._container, opacity);
				}
			},

			setElementSize: function (e, size) {
				e.style.width = size.x + 'px';
				e.style.height = size.y + 'px';
			},

			_initContainer: function () {
				var tilePane = this._map._container,
					first = tilePane.firstChild;

				if (!this._container) {
					this._container = L.DomUtil.create('div', 'leaflet-google-layer leaflet-top leaflet-left');
					this._container.id = '_GMapContainer_' + L.Util.stamp(this);
					this._container.style.zIndex = 'auto';
				}

				tilePane.insertBefore(this._container, first);

				this.setOpacity(this.options.opacity);
				this.setElementSize(this._container, this._map.getSize());
			},

			_initMapObject: function () {
				if (!this._ready || !this._container) return;
				this._google_center = new google.maps.LatLng(0, 0);
				var map = new google.maps.Map(this._container, {
					center: this._google_center,
					zoom: 0,
					tilt: 0,
					mapTypeId: google.maps.MapTypeId[this._type],
					disableDefaultUI: true,
					keyboardShortcuts: false,
					draggable: false,
					disableDoubleClickZoom: true,
					scrollwheel: false,
					streetViewControl: false,
					//styles: this.options.mapOptions.styles, info: not sure if needed as code 3 lines below is added
					backgroundColor: this.options.mapOptions.backgroundColor
				});
				if (mapsmarkerjspro.google_styling_json != 'disabled') {
					var styles = eval(mapsmarkerjspro.google_styling_json);
					map.setOptions({styles: styles});
				}
				var _this = this;
				this._reposition = google.maps.event.addListenerOnce(map, 'center_changed',
					function () { _this.onReposition(); });
				this._google = map;

				google.maps.event.addListenerOnce(map, 'idle',
					function () { _this._checkZoomLevels(); });
				google.maps.event.addListenerOnce(map, 'tilesloaded',

					function () { _this.fire('load'); });
				//Reporting that map-object was initialized.
				this.fire('MapObjectInitialized', {mapObject: map});
			},

			_checkZoomLevels: function () {
				//setting the zoom level on the Google map may result in a different zoom level than the one requested
				//(it won't go beyond the level for which they have data).
				// verify and make sure the zoom levels on both Leaflet and Google maps are consistent
				if ((this._map.getZoom() !== undefined) && (this._google.getZoom() !== this._map.getZoom())) {
					//zoom levels are out of sync. Set the leaflet zoom level to match the google one
					this._map.setZoom(this._google.getZoom());
				}
			},

			_reset: function () {
				this._initContainer();
			},

			_update: function () {
				if (!this._google) return;
				this._resize();

				var center = this._map.getCenter();
				var _center = new google.maps.LatLng(center.lat, center.lng);

				this._google.setCenter(_center);
				if (this._map.getZoom() !== undefined)
					this._google.setZoom(Math.round(this._map.getZoom()));

				this._checkZoomLevels();
			},

			_resize: function () {
				var size = this._map.getSize();
				if (this._container.style.width === size.x &&
						this._container.style.height === size.y)
					return;
				this.setElementSize(this._container, size);
				this.onReposition();
			},


			_handleZoomAnim: function (e) {
				var center = e.center;
				var _center = new google.maps.LatLng(center.lat, center.lng);

				this._google.setCenter(_center);
				this._google.setZoom(Math.round(e.zoom));
			},


			onReposition: function () {
				if (!this._google) return;
				google.maps.event.trigger(this._google, 'resize');
			}
		});

		L.Google.asyncWait = [];
		L.Google.asyncInitialize = function () {
			var i;
			for (i = 0; i < L.Google.asyncWait.length; i++) {
				var o = L.Google.asyncWait[i];
				o._ready = true;
				if (o._container) {
					o._initMapObject();
					o._update();
				}
			}
			L.Google.asyncWait = [];
		};
		L.Google.isGoogleMapsReady = function () {
			return !!window.google && !!window.google.maps && !!window.google.maps.Map;
		};
	}
};

/*
 * bing maps plugins - http://psha.org.ru/b/leaflet-plugins.html
*/
L.BingLayer = L.TileLayer.extend({
    options: {
        subdomains: [0, 1, 2, 3],
        type: this.type,
        attribution: '<a href="https://www.bing.com/maps/" target="_blank">Bing Maps</a>',
        culture: mapsmarkerjspro.bing_culture
    },
    initialize: function (key, options) {
        L.Util.setOptions(this, options);
        this._key = key;
        this._url = null;
		this._providers = [];
        this.metaRequested = false;
    },
    tile2quad: function (x, y, z) {
        var quad = '';
        for (var i = z; i > 0; i--) {
            var digit = 0;
            var mask = 1 << (i - 1);
            if ((x & mask) !== 0) digit += 1;
            if ((y & mask) !== 0) digit += 2;
            quad = quad + digit
        }
        return quad
    },
    getTileUrl: function (tilePoint) {
        var zoom = this._getZoomForUrl();
        var subdomains = this.options.subdomains,
            s = this.options.subdomains[Math.abs((tilePoint.x + tilePoint.y) % subdomains.length)];
        return this._url.replace('{subdomain}', s)
			.replace('{quadkey}', this.tile2quad(tilePoint.x, tilePoint.y, zoom))
			.replace('{culture}', this.options.culture)
    },
    loadMetadata: function () {
		if (this.metaRequested) return;
		this.metaRequested = true;
        var _this = this;
        var cbid = '_bing_metadata_' + L.Util.stamp(this);
        window[cbid] = function (meta) {
            window[cbid] = undefined;
            var e = document.getElementById(cbid);
            e.parentNode.removeChild(e);
            if (meta.errorDetails) {
                throw new Error(meta.errorDetails);
                return
            }
            _this.initMetadata(meta);
        };
		var urlScheme = (document.location.protocol === 'file:') ? 'http' : 'https';
		var url = urlScheme + '://dev.virtualearth.net/REST/v1/Imagery/Metadata/'
		 					+ this.options.type + '?include=ImageryProviders&jsonp=' + cbid +
							'&key=' + this._key + '&UriScheme=' + urlScheme;
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;
        script.id = cbid;
        document.getElementsByTagName('head')[0].appendChild(script)
    },
    initMetadata: function (meta) {
		var r = meta.resourceSets[0].resources[0];
        this.options.subdomains = r.imageUrlSubdomains;
        this._url = r.imageUrl;
		if (r.imageryProviders) {
        for (var i = 0; i < r.imageryProviders.length; i++) {
            var p = r.imageryProviders[i];
            for (var j = 0; j < p.coverageAreas.length; j++) {
                var c = p.coverageAreas[j];
                var coverage = {
                    zoomMin: c.zoomMin,
                    zoomMax: c.zoomMax,
                    active: false
                };
                var bounds = new L.LatLngBounds(new L.LatLng(c.bbox[0] + 0.01, c.bbox[1] + 0.01), new L.LatLng(c.bbox[2] - 0.01, c.bbox[3] - 0.01));
                coverage.bounds = bounds;
                coverage.attrib = p.attribution;
                this._providers.push(coverage)
            }
        }
		}
        this._update()
    },
    _update: function () {
        if (this._url === null || !this._map) return;
        this._update_attribution();
        L.TileLayer.prototype._update.apply(this, [])
    },
    _update_attribution: function () {
        var bounds = L.latLngBounds(this._map.getBounds().getSouthWest().wrap(),this._map.getBounds().getNorthEast().wrap());
        var zoom = this._map.getZoom();
        for (var i = 0; i < this._providers.length; i++) {
            var p = this._providers[i];
            if ((zoom <= p.zoomMax && zoom >= p.zoomMin) && bounds.intersects(p.bounds)) {
                if (!p.active) {
                    if (this._map.attributionControl) {
                        this._map.attributionControl.addAttribution(p.attrib);
                    }
                }
                p.active = true;
            } else {
                if (p.active) {
                    if (this._map.attributionControl) {
                        this._map.attributionControl.removeAttribution(p.attrib);
                    }
                }
                p.active = false;
            }
        }
    },
	onAdd: function (map) {
		this.loadMetadata();
		L.TileLayer.prototype.onAdd.apply(this, [map]);
	},
    onRemove: function (map) {
        for (var i = 0; i < this._providers.length; i++) {
            var p = this._providers[i];
            if (p.active) {
                if (this._map.attributionControl) {
                    this._map.attributionControl.removeAttribution(p.attrib);
                }
                p.active = false;
            }
        }
		L.TileLayer.prototype.onRemove.apply(this, [map]);
    }
});
L.bingLayer = function(key, options) {
    return new L.BingLayer(key, options);
};

/*
 Leaflet.markercluster, Provides Beautiful Animated Marker Clustering functionality for Leaflet, a JS library for interactive maps.
 https://github.com/Leaflet/Leaflet.markercluster
 (c) 2012-2013, Dave Leaver, smartrak
 https://github.com/Leaflet/Leaflet.markercluster (v1.0.6, 16/09/17)
*/
(function (window, document, undefined) {/*
 * L.MarkerClusterGroup extends L.FeatureGroup by clustering the markers contained within
 */

L.MarkerClusterGroup = L.FeatureGroup.extend({

	options: {
		maxClusterRadius: 80, //A cluster will cover at most this many pixels from its center
		iconCreateFunction: null,

		spiderfyOnMaxZoom: true,
		showCoverageOnHover: true,
		zoomToBoundsOnClick: true,
		singleMarkerMode: false,

		disableClusteringAtZoom: null,

		// Setting this to false prevents the removal of any clusters outside of the viewpoint, which
		// is the default behaviour for performance reasons.
		removeOutsideVisibleBounds: true,

		// Set to false to disable all animations (zoom and spiderfy).
		// If false, option animateAddingMarkers below has no effect.
		// If L.DomUtil.TRANSITION is falsy, this option has no effect.
		animate: true,

		//Whether to animate adding markers after adding the MarkerClusterGroup to the map
		// If you are adding individual markers set to true, if adding bulk markers leave false for massive performance gains.
		animateAddingMarkers: false,

		//Increase to increase the distance away that spiderfied markers appear from the center
		spiderfyDistanceMultiplier: 1,

		// Make it possible to specify a polyline options on a spider leg
		spiderLegPolylineOptions: { weight: 1.5, color: '#222', opacity: 0.5 },

		// When bulk adding layers, adds markers in chunks. Means addLayers may not add all the layers in the call, others will be loaded during setTimeouts
		chunkedLoading: false,
		chunkInterval: 200, // process markers for a maximum of ~ n milliseconds (then trigger the chunkProgress callback)
		chunkDelay: 50, // at the end of each interval, give n milliseconds back to system/browser
		chunkProgress: null, // progress callback: function(processed, total, elapsed) (e.g. for a progress indicator)

		//Options to pass to the L.Polygon constructor
		polygonOptions: {}
	},

	initialize: function (options) {
		L.Util.setOptions(this, options);
		if (!this.options.iconCreateFunction) {
			this.options.iconCreateFunction = this._defaultIconCreateFunction;
		}

		this._featureGroup = L.featureGroup();
		this._featureGroup.addEventParent(this);

		this._nonPointGroup = L.featureGroup();
		this._nonPointGroup.addEventParent(this);

		this._inZoomAnimation = 0;
		this._needsClustering = [];
		this._needsRemoving = []; //Markers removed while we aren't on the map need to be kept track of
		//The bounds of the currently shown area (from _getExpandedVisibleBounds) Updated on zoom/move
		this._currentShownBounds = null;

		this._queue = [];

		this._childMarkerEventHandlers = {
			'dragstart': this._childMarkerDragStart,
			'move': this._childMarkerMoved,
			'dragend': this._childMarkerDragEnd,
		};

		// Hook the appropriate animation methods.
		var animate = L.DomUtil.TRANSITION && this.options.animate;
		L.extend(this, animate ? this._withAnimation : this._noAnimation);
		// Remember which MarkerCluster class to instantiate (animated or not).
		this._markerCluster = animate ? L.MarkerCluster : L.MarkerClusterNonAnimated;
	},

	addLayer: function (layer) {

		if (layer instanceof L.LayerGroup) {
			return this.addLayers([layer]);
		}

		//Don't cluster non point data
		if (!layer.getLatLng) {
			this._nonPointGroup.addLayer(layer);
			this.fire('layeradd', { layer: layer });
			return this;
		}

		if (!this._map) {
			this._needsClustering.push(layer);
			this.fire('layeradd', { layer: layer });
			return this;
		}

		if (this.hasLayer(layer)) {
			return this;
		}


		//If we have already clustered we'll need to add this one to a cluster

		if (this._unspiderfy) {
			this._unspiderfy();
		}

		this._addLayer(layer, this._maxZoom);
		this.fire('layeradd', { layer: layer });

		// Refresh bounds and weighted positions.
		this._topClusterLevel._recalculateBounds();

		this._refreshClustersIcons();

		//Work out what is visible
		var visibleLayer = layer,
		    currentZoom = this._zoom;
		if (layer.__parent) {
			while (visibleLayer.__parent._zoom >= currentZoom) {
				visibleLayer = visibleLayer.__parent;
			}
		}

		if (this._currentShownBounds.contains(visibleLayer.getLatLng())) {
			if (this.options.animateAddingMarkers) {
				this._animationAddLayer(layer, visibleLayer);
			} else {
				this._animationAddLayerNonAnimated(layer, visibleLayer);
			}
		}
		return this;
	},

	removeLayer: function (layer) {

		if (layer instanceof L.LayerGroup) {
			return this.removeLayers([layer]);
		}

		//Non point layers
		if (!layer.getLatLng) {
			this._nonPointGroup.removeLayer(layer);
			this.fire('layerremove', { layer: layer });
			return this;
		}

		if (!this._map) {
			if (!this._arraySplice(this._needsClustering, layer) && this.hasLayer(layer)) {
				this._needsRemoving.push({ layer: layer, latlng: layer._latlng });
			}
			this.fire('layerremove', { layer: layer });
			return this;
		}

		if (!layer.__parent) {
			return this;
		}

		if (this._unspiderfy) {
			this._unspiderfy();
			this._unspiderfyLayer(layer);
		}

		//Remove the marker from clusters
		this._removeLayer(layer, true);
		this.fire('layerremove', { layer: layer });

		// Refresh bounds and weighted positions.
		this._topClusterLevel._recalculateBounds();

		this._refreshClustersIcons();

		layer.off(this._childMarkerEventHandlers, this);

		if (this._featureGroup.hasLayer(layer)) {
			this._featureGroup.removeLayer(layer);
			if (layer.clusterShow) {
				layer.clusterShow();
			}
		}

		return this;
	},

	//Takes an array of markers and adds them in bulk
	addLayers: function (layersArray, skipLayerAddEvent) {
		if (!L.Util.isArray(layersArray)) {
			return this.addLayer(layersArray);
		}

		var fg = this._featureGroup,
		    npg = this._nonPointGroup,
		    chunked = this.options.chunkedLoading,
		    chunkInterval = this.options.chunkInterval,
		    chunkProgress = this.options.chunkProgress,
		    l = layersArray.length,
		    offset = 0,
		    originalArray = true,
		    m;

		if (this._map) {
			var started = (new Date()).getTime();
			var process = L.bind(function () {
				var start = (new Date()).getTime();
				for (; offset < l; offset++) {
					if (chunked && offset % 200 === 0) {
						// every couple hundred markers, instrument the time elapsed since processing started:
						var elapsed = (new Date()).getTime() - start;
						if (elapsed > chunkInterval) {
							break; // been working too hard, time to take a break :-)
						}
					}

					m = layersArray[offset];

					// Group of layers, append children to layersArray and skip.
					// Side effects:
					// - Total increases, so chunkProgress ratio jumps backward.
					// - Groups are not included in this group, only their non-group child layers (hasLayer).
					// Changing array length while looping does not affect performance in current browsers:
					// http://jsperf.com/for-loop-changing-length/6
					if (m instanceof L.LayerGroup) {
						if (originalArray) {
							layersArray = layersArray.slice();
							originalArray = false;
						}
						this._extractNonGroupLayers(m, layersArray);
						l = layersArray.length;
						continue;
					}

					//Not point data, can't be clustered
					if (!m.getLatLng) {
						npg.addLayer(m);
						if (!skipLayerAddEvent) {
							this.fire('layeradd', { layer: m });
						}
						continue;
					}

					if (this.hasLayer(m)) {
						continue;
					}

					this._addLayer(m, this._maxZoom);
					if (!skipLayerAddEvent) {
						this.fire('layeradd', { layer: m });
					}

					//If we just made a cluster of size 2 then we need to remove the other marker from the map (if it is) or we never will
					if (m.__parent) {
						if (m.__parent.getChildCount() === 2) {
							var markers = m.__parent.getAllChildMarkers(),
							    otherMarker = markers[0] === m ? markers[1] : markers[0];
							fg.removeLayer(otherMarker);
						}
					}
				}

				if (chunkProgress) {
					// report progress and time elapsed:
					chunkProgress(offset, l, (new Date()).getTime() - started);
				}

				// Completed processing all markers.
				if (offset === l) {

					// Refresh bounds and weighted positions.
					this._topClusterLevel._recalculateBounds();

					this._refreshClustersIcons();

					this._topClusterLevel._recursivelyAddChildrenToMap(null, this._zoom, this._currentShownBounds);
				} else {
					setTimeout(process, this.options.chunkDelay);
				}
			}, this);

			process();
		} else {
			var needsClustering = this._needsClustering;

			for (; offset < l; offset++) {
				m = layersArray[offset];

				// Group of layers, append children to layersArray and skip.
				if (m instanceof L.LayerGroup) {
					if (originalArray) {
						layersArray = layersArray.slice();
						originalArray = false;
					}
					this._extractNonGroupLayers(m, layersArray);
					l = layersArray.length;
					continue;
				}

				//Not point data, can't be clustered
				if (!m.getLatLng) {
					npg.addLayer(m);
					continue;
				}

				if (this.hasLayer(m)) {
					continue;
				}

				needsClustering.push(m);
			}
		}
		return this;
	},

	//Takes an array of markers and removes them in bulk
	removeLayers: function (layersArray) {
		var i, m,
		    l = layersArray.length,
		    fg = this._featureGroup,
		    npg = this._nonPointGroup,
		    originalArray = true;

		if (!this._map) {
			for (i = 0; i < l; i++) {
				m = layersArray[i];

				// Group of layers, append children to layersArray and skip.
				if (m instanceof L.LayerGroup) {
					if (originalArray) {
						layersArray = layersArray.slice();
						originalArray = false;
					}
					this._extractNonGroupLayers(m, layersArray);
					l = layersArray.length;
					continue;
				}

				this._arraySplice(this._needsClustering, m);
				npg.removeLayer(m);
				if (this.hasLayer(m)) {
					this._needsRemoving.push({ layer: m, latlng: m._latlng });
				}
				this.fire('layerremove', { layer: m });
			}
			return this;
		}

		if (this._unspiderfy) {
			this._unspiderfy();

			// Work on a copy of the array, so that next loop is not affected.
			var layersArray2 = layersArray.slice(),
			    l2 = l;
			for (i = 0; i < l2; i++) {
				m = layersArray2[i];

				// Group of layers, append children to layersArray and skip.
				if (m instanceof L.LayerGroup) {
					this._extractNonGroupLayers(m, layersArray2);
					l2 = layersArray2.length;
					continue;
				}

				this._unspiderfyLayer(m);
			}
		}

		for (i = 0; i < l; i++) {
			m = layersArray[i];

			// Group of layers, append children to layersArray and skip.
			if (m instanceof L.LayerGroup) {
				if (originalArray) {
					layersArray = layersArray.slice();
					originalArray = false;
				}
				this._extractNonGroupLayers(m, layersArray);
				l = layersArray.length;
				continue;
			}

			if (!m.__parent) {
				npg.removeLayer(m);
				this.fire('layerremove', { layer: m });
				continue;
			}

			this._removeLayer(m, true, true);
			this.fire('layerremove', { layer: m });

			if (fg.hasLayer(m)) {
				fg.removeLayer(m);
				if (m.clusterShow) {
					m.clusterShow();
				}
			}
		}

		// Refresh bounds and weighted positions.
		this._topClusterLevel._recalculateBounds();

		this._refreshClustersIcons();

		//Fix up the clusters and markers on the map
		this._topClusterLevel._recursivelyAddChildrenToMap(null, this._zoom, this._currentShownBounds);

		return this;
	},

	//Removes all layers from the MarkerClusterGroup
	clearLayers: function () {
		//Need our own special implementation as the LayerGroup one doesn't work for us

		//If we aren't on the map (yet), blow away the markers we know of
		if (!this._map) {
			this._needsClustering = [];
			delete this._gridClusters;
			delete this._gridUnclustered;
		}

		if (this._noanimationUnspiderfy) {
			this._noanimationUnspiderfy();
		}

		//Remove all the visible layers
		this._featureGroup.clearLayers();
		this._nonPointGroup.clearLayers();

		this.eachLayer(function (marker) {
			marker.off(this._childMarkerEventHandlers, this);
			delete marker.__parent;
		}, this);

		if (this._map) {
			//Reset _topClusterLevel and the DistanceGrids
			this._generateInitialClusters();
		}

		return this;
	},

	//Override FeatureGroup.getBounds as it doesn't work
	getBounds: function () {
		var bounds = new L.LatLngBounds();

		if (this._topClusterLevel) {
			bounds.extend(this._topClusterLevel._bounds);
		}

		for (var i = this._needsClustering.length - 1; i >= 0; i--) {
			bounds.extend(this._needsClustering[i].getLatLng());
		}

		bounds.extend(this._nonPointGroup.getBounds());

		return bounds;
	},

	//Overrides LayerGroup.eachLayer
	eachLayer: function (method, context) {
		var markers = this._needsClustering.slice(),
			needsRemoving = this._needsRemoving,
			thisNeedsRemoving, i, j;

		if (this._topClusterLevel) {
			this._topClusterLevel.getAllChildMarkers(markers);
		}

		for (i = markers.length - 1; i >= 0; i--) {
			thisNeedsRemoving = true;

			for (j = needsRemoving.length - 1; j >= 0; j--) {
				if (needsRemoving[j].layer === markers[i]) {
					thisNeedsRemoving = false;
					break;
				}
			}

			if (thisNeedsRemoving) {
				method.call(context, markers[i]);
			}
		}

		this._nonPointGroup.eachLayer(method, context);
	},

	//Overrides LayerGroup.getLayers
	getLayers: function () {
		var layers = [];
		this.eachLayer(function (l) {
			layers.push(l);
		});
		return layers;
	},

	//Overrides LayerGroup.getLayer, WARNING: Really bad performance
	getLayer: function (id) {
		var result = null;

		id = parseInt(id, 10);

		this.eachLayer(function (l) {
			if (L.stamp(l) === id) {
				result = l;
			}
		});

		return result;
	},

	//Returns true if the given layer is in this MarkerClusterGroup
	hasLayer: function (layer) {
		if (!layer) {
			return false;
		}

		var i, anArray = this._needsClustering;

		for (i = anArray.length - 1; i >= 0; i--) {
			if (anArray[i] === layer) {
				return true;
			}
		}

		anArray = this._needsRemoving;
		for (i = anArray.length - 1; i >= 0; i--) {
			if (anArray[i].layer === layer) {
				return false;
			}
		}

		return !!(layer.__parent && layer.__parent._group === this) || this._nonPointGroup.hasLayer(layer);
	},

	//Zoom down to show the given layer (spiderfying if necessary) then calls the callback
	zoomToShowLayer: function (layer, callback) {

		if (typeof callback !== 'function') {
			callback = function () {};
		}

		var showMarker = function () {
			if ((layer._icon || layer.__parent._icon) && !this._inZoomAnimation) {
				this._map.off('moveend', showMarker, this);
				this.off('animationend', showMarker, this);

				if (layer._icon) {
					callback();
				} else if (layer.__parent._icon) {
					this.once('spiderfied', callback, this);
					layer.__parent.spiderfy();
				}
			}
		};

		if (layer._icon && this._map.getBounds().contains(layer.getLatLng())) {
			//Layer is visible ond on screen, immediate return
			callback();
		} else if (layer.__parent._zoom < Math.round(this._map._zoom)) {
			//Layer should be visible at this zoom level. It must not be on screen so just pan over to it
			this._map.on('moveend', showMarker, this);
			this._map.panTo(layer.getLatLng());
		} else {
			this._map.on('moveend', showMarker, this);
			this.on('animationend', showMarker, this);
			layer.__parent.zoomToBounds();
		}
	},

	//Overrides FeatureGroup.onAdd
	onAdd: function (map) {
		this._map = map;
		var i, l, layer;

		if (!isFinite(this._map.getMaxZoom())) {
			throw "Map has no maxZoom specified";
		}

		this._featureGroup.addTo(map);
		this._nonPointGroup.addTo(map);

		if (!this._gridClusters) {
			this._generateInitialClusters();
		}

		this._maxLat = map.options.crs.projection.MAX_LATITUDE;

		//Restore all the positions as they are in the MCG before removing them
		for (i = 0, l = this._needsRemoving.length; i < l; i++) {
			layer = this._needsRemoving[i];
			layer.newlatlng = layer.layer._latlng;
			layer.layer._latlng = layer.latlng;
		}
		//Remove them, then restore their new positions
		for (i = 0, l = this._needsRemoving.length; i < l; i++) {
			layer = this._needsRemoving[i];
			this._removeLayer(layer.layer, true);
			layer.layer._latlng = layer.newlatlng;
		}
		this._needsRemoving = [];

		//Remember the current zoom level and bounds
		this._zoom = Math.round(this._map._zoom);
		this._currentShownBounds = this._getExpandedVisibleBounds();

		this._map.on('zoomend', this._zoomEnd, this);
		this._map.on('moveend', this._moveEnd, this);

		if (this._spiderfierOnAdd) { //TODO FIXME: Not sure how to have spiderfier add something on here nicely
			this._spiderfierOnAdd();
		}

		this._bindEvents();

		//Actually add our markers to the map:
		l = this._needsClustering;
		this._needsClustering = [];
		this.addLayers(l, true);
	},

	//Overrides FeatureGroup.onRemove
	onRemove: function (map) {
		map.off('zoomend', this._zoomEnd, this);
		map.off('moveend', this._moveEnd, this);

		this._unbindEvents();

		//In case we are in a cluster animation
		this._map._mapPane.className = this._map._mapPane.className.replace(' leaflet-cluster-anim', '');

		if (this._spiderfierOnRemove) { //TODO FIXME: Not sure how to have spiderfier add something on here nicely
			this._spiderfierOnRemove();
		}

		delete this._maxLat;

		//Clean up all the layers we added to the map
		this._hideCoverage();
		this._featureGroup.remove();
		this._nonPointGroup.remove();

		this._featureGroup.clearLayers();

		this._map = null;
	},

	getVisibleParent: function (marker) {
		var vMarker = marker;
		while (vMarker && !vMarker._icon) {
			vMarker = vMarker.__parent;
		}
		return vMarker || null;
	},

	//Remove the given object from the given array
	_arraySplice: function (anArray, obj) {
		for (var i = anArray.length - 1; i >= 0; i--) {
			if (anArray[i] === obj) {
				anArray.splice(i, 1);
				return true;
			}
		}
	},

	/**
	 * Removes a marker from all _gridUnclustered zoom levels, starting at the supplied zoom.
	 * @param marker to be removed from _gridUnclustered.
	 * @param z integer bottom start zoom level (included)
	 * @private
	 */
	_removeFromGridUnclustered: function (marker, z) {
		var map = this._map,
		    gridUnclustered = this._gridUnclustered,
			minZoom = Math.floor(this._map.getMinZoom());

		for (; z >= minZoom; z--) {
			if (!gridUnclustered[z].removeObject(marker, map.project(marker.getLatLng(), z))) {
				break;
			}
		}
	},

	_childMarkerDragStart: function (e) {
		e.target.__dragStart = e.target._latlng;
	},

	_childMarkerMoved: function (e) {
		if (!this._ignoreMove && !e.target.__dragStart) {
			var isPopupOpen = e.target._popup && e.target._popup.isOpen();

			this._moveChild(e.target, e.oldLatLng, e.latlng);

			if (isPopupOpen) {
				e.target.openPopup();
			}
		}
	},

	_moveChild: function (layer, from, to) {
		layer._latlng = from;
		this.removeLayer(layer);

		layer._latlng = to;
		this.addLayer(layer);
	},

	_childMarkerDragEnd: function (e) {
		if (e.target.__dragStart) {
			this._moveChild(e.target, e.target.__dragStart, e.target._latlng);
		}
		delete e.target.__dragStart;
	},


	//Internal function for removing a marker from everything.
	//dontUpdateMap: set to true if you will handle updating the map manually (for bulk functions)
	_removeLayer: function (marker, removeFromDistanceGrid, dontUpdateMap) {
		var gridClusters = this._gridClusters,
			gridUnclustered = this._gridUnclustered,
			fg = this._featureGroup,
			map = this._map,
			minZoom = Math.floor(this._map.getMinZoom());

		//Remove the marker from distance clusters it might be in
		if (removeFromDistanceGrid) {
			this._removeFromGridUnclustered(marker, this._maxZoom);
		}

		//Work our way up the clusters removing them as we go if required
		var cluster = marker.__parent,
			markers = cluster._markers,
			otherMarker;

		//Remove the marker from the immediate parents marker list
		this._arraySplice(markers, marker);

		while (cluster) {
			cluster._childCount--;
			cluster._boundsNeedUpdate = true;

			if (cluster._zoom < minZoom) {
				//Top level, do nothing
				break;
			} else if (removeFromDistanceGrid && cluster._childCount <= 1) { //Cluster no longer required
				//We need to push the other marker up to the parent
				otherMarker = cluster._markers[0] === marker ? cluster._markers[1] : cluster._markers[0];

				//Update distance grid
				gridClusters[cluster._zoom].removeObject(cluster, map.project(cluster._cLatLng, cluster._zoom));
				gridUnclustered[cluster._zoom].addObject(otherMarker, map.project(otherMarker.getLatLng(), cluster._zoom));

				//Move otherMarker up to parent
				this._arraySplice(cluster.__parent._childClusters, cluster);
				cluster.__parent._markers.push(otherMarker);
				otherMarker.__parent = cluster.__parent;

				if (cluster._icon) {
					//Cluster is currently on the map, need to put the marker on the map instead
					fg.removeLayer(cluster);
					if (!dontUpdateMap) {
						fg.addLayer(otherMarker);
					}
				}
			} else {
				cluster._iconNeedsUpdate = true;
			}

			cluster = cluster.__parent;
		}

		delete marker.__parent;
	},

	_isOrIsParent: function (el, oel) {
		while (oel) {
			if (el === oel) {
				return true;
			}
			oel = oel.parentNode;
		}
		return false;
	},

	//Override L.Evented.fire
	fire: function (type, data, propagate) {
		if (data && data.layer instanceof L.MarkerCluster) {
			//Prevent multiple clustermouseover/off events if the icon is made up of stacked divs (Doesn't work in ie <= 8, no relatedTarget)
			if (data.originalEvent && this._isOrIsParent(data.layer._icon, data.originalEvent.relatedTarget)) {
				return;
			}
			type = 'cluster' + type;
		}

		L.FeatureGroup.prototype.fire.call(this, type, data, propagate);
	},

	//Override L.Evented.listens
	listens: function (type, propagate) {
		return L.FeatureGroup.prototype.listens.call(this, type, propagate) || L.FeatureGroup.prototype.listens.call(this, 'cluster' + type, propagate);
	},

	//Default functionality
	_defaultIconCreateFunction: function (cluster) {
		var childCount = cluster.getChildCount();

		var c = ' marker-cluster-';
		if (childCount < 10) {
			c += 'small';
		} else if (childCount < 100) {
			c += 'medium';
		} else {
			c += 'large';
		}

		return new L.DivIcon({ html: '<div><span>' + childCount + '</span></div>', className: 'marker-cluster' + c, iconSize: new L.Point(40, 40) });
	},

	_bindEvents: function () {
		var map = this._map,
		    spiderfyOnMaxZoom = this.options.spiderfyOnMaxZoom,
		    showCoverageOnHover = this.options.showCoverageOnHover,
		    zoomToBoundsOnClick = this.options.zoomToBoundsOnClick;

		//Zoom on cluster click or spiderfy if we are at the lowest level
		if (spiderfyOnMaxZoom || zoomToBoundsOnClick) {
			this.on('clusterclick', this._zoomOrSpiderfy, this);
		}

		//Show convex hull (boundary) polygon on mouse over
		if (showCoverageOnHover) {
			this.on('clustermouseover', this._showCoverage, this);
			this.on('clustermouseout', this._hideCoverage, this);
			map.on('zoomend', this._hideCoverage, this);
		}
	},

	_zoomOrSpiderfy: function (e) {
		var cluster = e.layer,
		    bottomCluster = cluster;

		while (bottomCluster._childClusters.length === 1) {
			bottomCluster = bottomCluster._childClusters[0];
		}

		if (bottomCluster._zoom === this._maxZoom &&
			bottomCluster._childCount === cluster._childCount &&
			this.options.spiderfyOnMaxZoom) {

			// All child markers are contained in a single cluster from this._maxZoom to this cluster.
			cluster.spiderfy();
		} else if (this.options.zoomToBoundsOnClick) {
			cluster.zoomToBounds();
		}

		// Focus the map again for keyboard users.
		if (e.originalEvent && e.originalEvent.keyCode === 13) {
			this._map._container.focus();
		}
	},

	_showCoverage: function (e) {
		var map = this._map;
		if (this._inZoomAnimation) {
			return;
		}
		if (this._shownPolygon) {
			map.removeLayer(this._shownPolygon);
		}
		if (e.layer.getChildCount() > 2 && e.layer !== this._spiderfied) {
			this._shownPolygon = new L.Polygon(e.layer.getConvexHull(), this.options.polygonOptions);
			map.addLayer(this._shownPolygon);
		}
	},

	_hideCoverage: function () {
		if (this._shownPolygon) {
			this._map.removeLayer(this._shownPolygon);
			this._shownPolygon = null;
		}
	},

	_unbindEvents: function () {
		var spiderfyOnMaxZoom = this.options.spiderfyOnMaxZoom,
			showCoverageOnHover = this.options.showCoverageOnHover,
			zoomToBoundsOnClick = this.options.zoomToBoundsOnClick,
			map = this._map;

		if (spiderfyOnMaxZoom || zoomToBoundsOnClick) {
			this.off('clusterclick', this._zoomOrSpiderfy, this);
		}
		if (showCoverageOnHover) {
			this.off('clustermouseover', this._showCoverage, this);
			this.off('clustermouseout', this._hideCoverage, this);
			map.off('zoomend', this._hideCoverage, this);
		}
	},

	_zoomEnd: function () {
		if (!this._map) { //May have been removed from the map by a zoomEnd handler
			return;
		}
		this._mergeSplitClusters();

		this._zoom = Math.round(this._map._zoom);
		this._currentShownBounds = this._getExpandedVisibleBounds();
	},

	_moveEnd: function () {
		if (this._inZoomAnimation) {
			return;
		}

		var newBounds = this._getExpandedVisibleBounds();

		this._topClusterLevel._recursivelyRemoveChildrenFromMap(this._currentShownBounds, Math.floor(this._map.getMinZoom()), this._zoom, newBounds);
		this._topClusterLevel._recursivelyAddChildrenToMap(null, Math.round(this._map._zoom), newBounds);

		this._currentShownBounds = newBounds;
		return;
	},

	_generateInitialClusters: function () {
		var maxZoom = Math.ceil(this._map.getMaxZoom()),
			minZoom = Math.floor(this._map.getMinZoom()),
			radius = this.options.maxClusterRadius,
			radiusFn = radius;

		//If we just set maxClusterRadius to a single number, we need to create
		//a simple function to return that number. Otherwise, we just have to
		//use the function we've passed in.
		if (typeof radius !== "function") {
			radiusFn = function () { return radius; };
		}

		if (this.options.disableClusteringAtZoom !== null) {
			maxZoom = this.options.disableClusteringAtZoom - 1;
		}
		this._maxZoom = maxZoom;
		this._gridClusters = {};
		this._gridUnclustered = {};

		//Set up DistanceGrids for each zoom
		for (var zoom = maxZoom; zoom >= minZoom; zoom--) {
			this._gridClusters[zoom] = new L.DistanceGrid(radiusFn(zoom));
			this._gridUnclustered[zoom] = new L.DistanceGrid(radiusFn(zoom));
		}

		// Instantiate the appropriate L.MarkerCluster class (animated or not).
		this._topClusterLevel = new this._markerCluster(this, minZoom - 1);
	},

	//Zoom: Zoom to start adding at (Pass this._maxZoom to start at the bottom)
	_addLayer: function (layer, zoom) {
		var gridClusters = this._gridClusters,
		    gridUnclustered = this._gridUnclustered,
			minZoom = Math.floor(this._map.getMinZoom()),
		    markerPoint, z;

		if (this.options.singleMarkerMode) {
			this._overrideMarkerIcon(layer);
		}

		layer.on(this._childMarkerEventHandlers, this);

		//Find the lowest zoom level to slot this one in
		for (; zoom >= minZoom; zoom--) {
			markerPoint = this._map.project(layer.getLatLng(), zoom); // calculate pixel position

			//Try find a cluster close by
			var closest = gridClusters[zoom].getNearObject(markerPoint);
			if (closest) {
				closest._addChild(layer);
				layer.__parent = closest;
				return;
			}

			//Try find a marker close by to form a new cluster with
			closest = gridUnclustered[zoom].getNearObject(markerPoint);
			if (closest) {
				var parent = closest.__parent;
				if (parent) {
					this._removeLayer(closest, false);
				}

				//Create new cluster with these 2 in it

				var newCluster = new this._markerCluster(this, zoom, closest, layer);
				gridClusters[zoom].addObject(newCluster, this._map.project(newCluster._cLatLng, zoom));
				closest.__parent = newCluster;
				layer.__parent = newCluster;

				//First create any new intermediate parent clusters that don't exist
				var lastParent = newCluster;
				for (z = zoom - 1; z > parent._zoom; z--) {
					lastParent = new this._markerCluster(this, z, lastParent);
					gridClusters[z].addObject(lastParent, this._map.project(closest.getLatLng(), z));
				}
				parent._addChild(lastParent);

				//Remove closest from this zoom level and any above that it is in, replace with newCluster
				this._removeFromGridUnclustered(closest, zoom);

				return;
			}

			//Didn't manage to cluster in at this zoom, record us as a marker here and continue upwards
			gridUnclustered[zoom].addObject(layer, markerPoint);
		}

		//Didn't get in anything, add us to the top
		this._topClusterLevel._addChild(layer);
		layer.__parent = this._topClusterLevel;
		return;
	},

	/**
	 * Refreshes the icon of all "dirty" visible clusters.
	 * Non-visible "dirty" clusters will be updated when they are added to the map.
	 * @private
	 */
	_refreshClustersIcons: function () {
		this._featureGroup.eachLayer(function (c) {
			if (c instanceof L.MarkerCluster && c._iconNeedsUpdate) {
				c._updateIcon();
			}
		});
	},

	//Enqueue code to fire after the marker expand/contract has happened
	_enqueue: function (fn) {
		this._queue.push(fn);
		if (!this._queueTimeout) {
			this._queueTimeout = setTimeout(L.bind(this._processQueue, this), 300);
		}
	},
	_processQueue: function () {
		for (var i = 0; i < this._queue.length; i++) {
			this._queue[i].call(this);
		}
		this._queue.length = 0;
		clearTimeout(this._queueTimeout);
		this._queueTimeout = null;
	},

	//Merge and split any existing clusters that are too big or small
	_mergeSplitClusters: function () {
		var mapZoom = Math.round(this._map._zoom);

		//In case we are starting to split before the animation finished
		this._processQueue();

		if (this._zoom < mapZoom && this._currentShownBounds.intersects(this._getExpandedVisibleBounds())) { //Zoom in, split
			this._animationStart();
			//Remove clusters now off screen
			this._topClusterLevel._recursivelyRemoveChildrenFromMap(this._currentShownBounds, Math.floor(this._map.getMinZoom()), this._zoom, this._getExpandedVisibleBounds());

			this._animationZoomIn(this._zoom, mapZoom);

		} else if (this._zoom > mapZoom) { //Zoom out, merge
			this._animationStart();

			this._animationZoomOut(this._zoom, mapZoom);
		} else {
			this._moveEnd();
		}
	},

	//Gets the maps visible bounds expanded in each direction by the size of the screen (so the user cannot see an area we do not cover in one pan)
	_getExpandedVisibleBounds: function () {
		if (!this.options.removeOutsideVisibleBounds) {
			return this._mapBoundsInfinite;
		} else if (L.Browser.mobile) {
			return this._checkBoundsMaxLat(this._map.getBounds());
		}

		return this._checkBoundsMaxLat(this._map.getBounds().pad(1)); // Padding expands the bounds by its own dimensions but scaled with the given factor.
	},

	/**
	 * Expands the latitude to Infinity (or -Infinity) if the input bounds reach the map projection maximum defined latitude
	 * (in the case of Web/Spherical Mercator, it is 85.0511287798 / see https://en.wikipedia.org/wiki/Web_Mercator#Formulas).
	 * Otherwise, the removeOutsideVisibleBounds option will remove markers beyond that limit, whereas the same markers without
	 * this option (or outside MCG) will have their position floored (ceiled) by the projection and rendered at that limit,
	 * making the user think that MCG "eats" them and never displays them again.
	 * @param bounds L.LatLngBounds
	 * @returns {L.LatLngBounds}
	 * @private
	 */
	_checkBoundsMaxLat: function (bounds) {
		var maxLat = this._maxLat;

		if (maxLat !== undefined) {
			if (bounds.getNorth() >= maxLat) {
				bounds._northEast.lat = Infinity;
			}
			if (bounds.getSouth() <= -maxLat) {
				bounds._southWest.lat = -Infinity;
			}
		}

		return bounds;
	},

	//Shared animation code
	_animationAddLayerNonAnimated: function (layer, newCluster) {
		if (newCluster === layer) {
			this._featureGroup.addLayer(layer);
		} else if (newCluster._childCount === 2) {
			newCluster._addToMap();

			var markers = newCluster.getAllChildMarkers();
			this._featureGroup.removeLayer(markers[0]);
			this._featureGroup.removeLayer(markers[1]);
		} else {
			newCluster._updateIcon();
		}
	},

	/**
	 * Extracts individual (i.e. non-group) layers from a Layer Group.
	 * @param group to extract layers from.
	 * @param output {Array} in which to store the extracted layers.
	 * @returns {*|Array}
	 * @private
	 */
	_extractNonGroupLayers: function (group, output) {
		var layers = group.getLayers(),
		    i = 0,
		    layer;

		output = output || [];

		for (; i < layers.length; i++) {
			layer = layers[i];

			if (layer instanceof L.LayerGroup) {
				this._extractNonGroupLayers(layer, output);
				continue;
			}

			output.push(layer);
		}

		return output;
	},

	/**
	 * Implements the singleMarkerMode option.
	 * @param layer Marker to re-style using the Clusters iconCreateFunction.
	 * @returns {L.Icon} The newly created icon.
	 * @private
	 */
	_overrideMarkerIcon: function (layer) {
		var icon = layer.options.icon = this.options.iconCreateFunction({
			getChildCount: function () {
				return 1;
			},
			getAllChildMarkers: function () {
				return [layer];
			}
		});

		return icon;
	}
});

// Constant bounds used in case option "removeOutsideVisibleBounds" is set to false.
L.MarkerClusterGroup.include({
	_mapBoundsInfinite: new L.LatLngBounds(new L.LatLng(-Infinity, -Infinity), new L.LatLng(Infinity, Infinity))
});

L.MarkerClusterGroup.include({
	_noAnimation: {
		//Non Animated versions of everything
		_animationStart: function () {
			//Do nothing...
		},
		_animationZoomIn: function (previousZoomLevel, newZoomLevel) {
			this._topClusterLevel._recursivelyRemoveChildrenFromMap(this._currentShownBounds, Math.floor(this._map.getMinZoom()), previousZoomLevel);
			this._topClusterLevel._recursivelyAddChildrenToMap(null, newZoomLevel, this._getExpandedVisibleBounds());

			//We didn't actually animate, but we use this event to mean "clustering animations have finished"
			this.fire('animationend');
		},
		_animationZoomOut: function (previousZoomLevel, newZoomLevel) {
			this._topClusterLevel._recursivelyRemoveChildrenFromMap(this._currentShownBounds, Math.floor(this._map.getMinZoom()), previousZoomLevel);
			this._topClusterLevel._recursivelyAddChildrenToMap(null, newZoomLevel, this._getExpandedVisibleBounds());

			//We didn't actually animate, but we use this event to mean "clustering animations have finished"
			this.fire('animationend');
		},
		_animationAddLayer: function (layer, newCluster) {
			this._animationAddLayerNonAnimated(layer, newCluster);
		}
	},

	_withAnimation: {
		//Animated versions here
		_animationStart: function () {
			this._map._mapPane.className += ' leaflet-cluster-anim';
			this._inZoomAnimation++;
		},

		_animationZoomIn: function (previousZoomLevel, newZoomLevel) {
			var bounds = this._getExpandedVisibleBounds(),
			    fg = this._featureGroup,
				minZoom = Math.floor(this._map.getMinZoom()),
			    i;

			this._ignoreMove = true;

			//Add all children of current clusters to map and remove those clusters from map
			this._topClusterLevel._recursively(bounds, previousZoomLevel, minZoom, function (c) {
				var startPos = c._latlng,
				    markers  = c._markers,
				    m;

				if (!bounds.contains(startPos)) {
					startPos = null;
				}

				if (c._isSingleParent() && previousZoomLevel + 1 === newZoomLevel) { //Immediately add the new child and remove us
					fg.removeLayer(c);
					c._recursivelyAddChildrenToMap(null, newZoomLevel, bounds);
				} else {
					//Fade out old cluster
					c.clusterHide();
					c._recursivelyAddChildrenToMap(startPos, newZoomLevel, bounds);
				}

				//Remove all markers that aren't visible any more
				//TODO: Do we actually need to do this on the higher levels too?
				for (i = markers.length - 1; i >= 0; i--) {
					m = markers[i];
					if (!bounds.contains(m._latlng)) {
						fg.removeLayer(m);
					}
				}

			});

			this._forceLayout();

			//Update opacities
			this._topClusterLevel._recursivelyBecomeVisible(bounds, newZoomLevel);
			//TODO Maybe? Update markers in _recursivelyBecomeVisible
			fg.eachLayer(function (n) {
				if (!(n instanceof L.MarkerCluster) && n._icon) {
					n.clusterShow();
				}
			});

			//update the positions of the just added clusters/markers
			this._topClusterLevel._recursively(bounds, previousZoomLevel, newZoomLevel, function (c) {
				c._recursivelyRestoreChildPositions(newZoomLevel);
			});

			this._ignoreMove = false;

			//Remove the old clusters and close the zoom animation
			this._enqueue(function () {
				//update the positions of the just added clusters/markers
				this._topClusterLevel._recursively(bounds, previousZoomLevel, minZoom, function (c) {
					fg.removeLayer(c);
					c.clusterShow();
				});

				this._animationEnd();
			});
		},

		_animationZoomOut: function (previousZoomLevel, newZoomLevel) {
			this._animationZoomOutSingle(this._topClusterLevel, previousZoomLevel - 1, newZoomLevel);

			//Need to add markers for those that weren't on the map before but are now
			this._topClusterLevel._recursivelyAddChildrenToMap(null, newZoomLevel, this._getExpandedVisibleBounds());
			//Remove markers that were on the map before but won't be now
			this._topClusterLevel._recursivelyRemoveChildrenFromMap(this._currentShownBounds, Math.floor(this._map.getMinZoom()), previousZoomLevel, this._getExpandedVisibleBounds());
		},

		_animationAddLayer: function (layer, newCluster) {
			var me = this,
			    fg = this._featureGroup;

			fg.addLayer(layer);
			if (newCluster !== layer) {
				if (newCluster._childCount > 2) { //Was already a cluster

					newCluster._updateIcon();
					this._forceLayout();
					this._animationStart();

					layer._setPos(this._map.latLngToLayerPoint(newCluster.getLatLng()));
					layer.clusterHide();

					this._enqueue(function () {
						fg.removeLayer(layer);
						layer.clusterShow();

						me._animationEnd();
					});

				} else { //Just became a cluster
					this._forceLayout();

					me._animationStart();
					me._animationZoomOutSingle(newCluster, this._map.getMaxZoom(), this._zoom);
				}
			}
		}
	},

	// Private methods for animated versions.
	_animationZoomOutSingle: function (cluster, previousZoomLevel, newZoomLevel) {
		var bounds = this._getExpandedVisibleBounds(),
			minZoom = Math.floor(this._map.getMinZoom());

		//Animate all of the markers in the clusters to move to their cluster center point
		cluster._recursivelyAnimateChildrenInAndAddSelfToMap(bounds, minZoom, previousZoomLevel + 1, newZoomLevel);

		var me = this;

		//Update the opacity (If we immediately set it they won't animate)
		this._forceLayout();
		cluster._recursivelyBecomeVisible(bounds, newZoomLevel);

		//TODO: Maybe use the transition timing stuff to make this more reliable
		//When the animations are done, tidy up
		this._enqueue(function () {

			//This cluster stopped being a cluster before the timeout fired
			if (cluster._childCount === 1) {
				var m = cluster._markers[0];
				//If we were in a cluster animation at the time then the opacity and position of our child could be wrong now, so fix it
				this._ignoreMove = true;
				m.setLatLng(m.getLatLng());
				this._ignoreMove = false;
				if (m.clusterShow) {
					m.clusterShow();
				}
			} else {
				cluster._recursively(bounds, newZoomLevel, minZoom, function (c) {
					c._recursivelyRemoveChildrenFromMap(bounds, minZoom, previousZoomLevel + 1);
				});
			}
			me._animationEnd();
		});
	},

	_animationEnd: function () {
		if (this._map) {
			this._map._mapPane.className = this._map._mapPane.className.replace(' leaflet-cluster-anim', '');
		}
		this._inZoomAnimation--;
		this.fire('animationend');
	},

	//Force a browser layout of stuff in the map
	// Should apply the current opacity and location to all elements so we can update them again for an animation
	_forceLayout: function () {
		//In my testing this works, infact offsetWidth of any element seems to work.
		//Could loop all this._layers and do this for each _icon if it stops working

		L.Util.falseFn(document.body.offsetWidth);
	}
});

L.markerClusterGroup = function (options) {
	return new L.MarkerClusterGroup(options);
};


L.MarkerCluster = L.Marker.extend({
	initialize: function (group, zoom, a, b) {

		L.Marker.prototype.initialize.call(this, a ? (a._cLatLng || a.getLatLng()) : new L.LatLng(0, 0), { icon: this });


		this._group = group;
		this._zoom = zoom;

		this._markers = [];
		this._childClusters = [];
		this._childCount = 0;
		this._iconNeedsUpdate = true;
		this._boundsNeedUpdate = true;

		this._bounds = new L.LatLngBounds();

		if (a) {
			this._addChild(a);
		}
		if (b) {
			this._addChild(b);
		}
	},

	//Recursively retrieve all child markers of this cluster
	getAllChildMarkers: function (storageArray) {
		storageArray = storageArray || [];

		for (var i = this._childClusters.length - 1; i >= 0; i--) {
			this._childClusters[i].getAllChildMarkers(storageArray);
		}

		for (var j = this._markers.length - 1; j >= 0; j--) {
			storageArray.push(this._markers[j]);
		}

		return storageArray;
	},

	//Returns the count of how many child markers we have
	getChildCount: function () {
		return this._childCount;
	},

	//Zoom to the minimum of showing all of the child markers, or the extents of this cluster
	zoomToBounds: function (fitBoundsOptions) {
		var childClusters = this._childClusters.slice(),
			map = this._group._map,
			boundsZoom = map.getBoundsZoom(this._bounds),
			zoom = this._zoom + 1,
			mapZoom = map.getZoom(),
			i;

		//calculate how far we need to zoom down to see all of the markers
		while (childClusters.length > 0 && boundsZoom > zoom) {
			zoom++;
			var newClusters = [];
			for (i = 0; i < childClusters.length; i++) {
				newClusters = newClusters.concat(childClusters[i]._childClusters);
			}
			childClusters = newClusters;
		}

		if (boundsZoom > zoom) {
			this._group._map.setView(this._latlng, zoom);
		} else if (boundsZoom <= mapZoom) { //If fitBounds wouldn't zoom us down, zoom us down instead
			this._group._map.setView(this._latlng, mapZoom + 1);
		} else {
			this._group._map.fitBounds(this._bounds, fitBoundsOptions);
		}
	},

	getBounds: function () {
		var bounds = new L.LatLngBounds();
		bounds.extend(this._bounds);
		return bounds;
	},

	_updateIcon: function () {
		this._iconNeedsUpdate = true;
		if (this._icon) {
			this.setIcon(this);
		}
	},

	//Cludge for Icon, we pretend to be an icon for performance
	createIcon: function () {
		if (this._iconNeedsUpdate) {
			this._iconObj = this._group.options.iconCreateFunction(this);
			this._iconNeedsUpdate = false;
		}
		return this._iconObj.createIcon();
	},
	createShadow: function () {
		return this._iconObj.createShadow();
	},


	_addChild: function (new1, isNotificationFromChild) {

		this._iconNeedsUpdate = true;

		this._boundsNeedUpdate = true;
		this._setClusterCenter(new1);

		if (new1 instanceof L.MarkerCluster) {
			if (!isNotificationFromChild) {
				this._childClusters.push(new1);
				new1.__parent = this;
			}
			this._childCount += new1._childCount;
		} else {
			if (!isNotificationFromChild) {
				this._markers.push(new1);
			}
			this._childCount++;
		}

		if (this.__parent) {
			this.__parent._addChild(new1, true);
		}
	},

	/**
	 * Makes sure the cluster center is set. If not, uses the child center if it is a cluster, or the marker position.
	 * @param child L.MarkerCluster|L.Marker that will be used as cluster center if not defined yet.
	 * @private
	 */
	_setClusterCenter: function (child) {
		if (!this._cLatLng) {
			// when clustering, take position of the first point as the cluster center
			this._cLatLng = child._cLatLng || child._latlng;
		}
	},

	/**
	 * Assigns impossible bounding values so that the next extend entirely determines the new bounds.
	 * This method avoids having to trash the previous L.LatLngBounds object and to create a new one, which is much slower for this class.
	 * As long as the bounds are not extended, most other methods would probably fail, as they would with bounds initialized but not extended.
	 * @private
	 */
	_resetBounds: function () {
		var bounds = this._bounds;

		if (bounds._southWest) {
			bounds._southWest.lat = Infinity;
			bounds._southWest.lng = Infinity;
		}
		if (bounds._northEast) {
			bounds._northEast.lat = -Infinity;
			bounds._northEast.lng = -Infinity;
		}
	},

	_recalculateBounds: function () {
		var markers = this._markers,
		    childClusters = this._childClusters,
		    latSum = 0,
		    lngSum = 0,
		    totalCount = this._childCount,
		    i, child, childLatLng, childCount;

		// Case where all markers are removed from the map and we are left with just an empty _topClusterLevel.
		if (totalCount === 0) {
			return;
		}

		// Reset rather than creating a new object, for performance.
		this._resetBounds();

		// Child markers.
		for (i = 0; i < markers.length; i++) {
			childLatLng = markers[i]._latlng;

			this._bounds.extend(childLatLng);

			latSum += childLatLng.lat;
			lngSum += childLatLng.lng;
		}

		// Child clusters.
		for (i = 0; i < childClusters.length; i++) {
			child = childClusters[i];

			// Re-compute child bounds and weighted position first if necessary.
			if (child._boundsNeedUpdate) {
				child._recalculateBounds();
			}

			this._bounds.extend(child._bounds);

			childLatLng = child._wLatLng;
			childCount = child._childCount;

			latSum += childLatLng.lat * childCount;
			lngSum += childLatLng.lng * childCount;
		}

		this._latlng = this._wLatLng = new L.LatLng(latSum / totalCount, lngSum / totalCount);

		// Reset dirty flag.
		this._boundsNeedUpdate = false;
	},

	//Set our markers position as given and add it to the map
	_addToMap: function (startPos) {
		if (startPos) {
			this._backupLatlng = this._latlng;
			this.setLatLng(startPos);
		}
		this._group._featureGroup.addLayer(this);
	},

	_recursivelyAnimateChildrenIn: function (bounds, center, maxZoom) {
		this._recursively(bounds, this._group._map.getMinZoom(), maxZoom - 1,
			function (c) {
				var markers = c._markers,
					i, m;
				for (i = markers.length - 1; i >= 0; i--) {
					m = markers[i];

					//Only do it if the icon is still on the map
					if (m._icon) {
						m._setPos(center);
						m.clusterHide();
					}
				}
			},
			function (c) {
				var childClusters = c._childClusters,
					j, cm;
				for (j = childClusters.length - 1; j >= 0; j--) {
					cm = childClusters[j];
					if (cm._icon) {
						cm._setPos(center);
						cm.clusterHide();
					}
				}
			}
		);
	},

	_recursivelyAnimateChildrenInAndAddSelfToMap: function (bounds, mapMinZoom, previousZoomLevel, newZoomLevel) {
		this._recursively(bounds, newZoomLevel, mapMinZoom,
			function (c) {
				c._recursivelyAnimateChildrenIn(bounds, c._group._map.latLngToLayerPoint(c.getLatLng()).round(), previousZoomLevel);

				//TODO: depthToAnimateIn affects _isSingleParent, if there is a multizoom we may/may not be.
				//As a hack we only do a animation free zoom on a single level zoom, if someone does multiple levels then we always animate
				if (c._isSingleParent() && previousZoomLevel - 1 === newZoomLevel) {
					c.clusterShow();
					c._recursivelyRemoveChildrenFromMap(bounds, mapMinZoom, previousZoomLevel); //Immediately remove our children as we are replacing them. TODO previousBounds not bounds
				} else {
					c.clusterHide();
				}

				c._addToMap();
			}
		);
	},

	_recursivelyBecomeVisible: function (bounds, zoomLevel) {
		this._recursively(bounds, this._group._map.getMinZoom(), zoomLevel, null, function (c) {
			c.clusterShow();
		});
	},

	_recursivelyAddChildrenToMap: function (startPos, zoomLevel, bounds) {
		this._recursively(bounds, this._group._map.getMinZoom() - 1, zoomLevel,
			function (c) {
				if (zoomLevel === c._zoom) {
					return;
				}

				//Add our child markers at startPos (so they can be animated out)
				for (var i = c._markers.length - 1; i >= 0; i--) {
					var nm = c._markers[i];

					if (!bounds.contains(nm._latlng)) {
						continue;
					}

					if (startPos) {
						nm._backupLatlng = nm.getLatLng();

						nm.setLatLng(startPos);
						if (nm.clusterHide) {
							nm.clusterHide();
						}
					}

					c._group._featureGroup.addLayer(nm);
				}
			},
			function (c) {
				c._addToMap(startPos);
			}
		);
	},

	_recursivelyRestoreChildPositions: function (zoomLevel) {
		//Fix positions of child markers
		for (var i = this._markers.length - 1; i >= 0; i--) {
			var nm = this._markers[i];
			if (nm._backupLatlng) {
				nm.setLatLng(nm._backupLatlng);
				delete nm._backupLatlng;
			}
		}

		if (zoomLevel - 1 === this._zoom) {
			//Reposition child clusters
			for (var j = this._childClusters.length - 1; j >= 0; j--) {
				this._childClusters[j]._restorePosition();
			}
		} else {
			for (var k = this._childClusters.length - 1; k >= 0; k--) {
				this._childClusters[k]._recursivelyRestoreChildPositions(zoomLevel);
			}
		}
	},

	_restorePosition: function () {
		if (this._backupLatlng) {
			this.setLatLng(this._backupLatlng);
			delete this._backupLatlng;
		}
	},

	//exceptBounds: If set, don't remove any markers/clusters in it
	_recursivelyRemoveChildrenFromMap: function (previousBounds, mapMinZoom, zoomLevel, exceptBounds) {
		var m, i;
		this._recursively(previousBounds, mapMinZoom - 1, zoomLevel - 1,
			function (c) {
				//Remove markers at every level
				for (i = c._markers.length - 1; i >= 0; i--) {
					m = c._markers[i];
					if (!exceptBounds || !exceptBounds.contains(m._latlng)) {
						c._group._featureGroup.removeLayer(m);
						if (m.clusterShow) {
							m.clusterShow();
						}
					}
				}
			},
			function (c) {
				//Remove child clusters at just the bottom level
				for (i = c._childClusters.length - 1; i >= 0; i--) {
					m = c._childClusters[i];
					if (!exceptBounds || !exceptBounds.contains(m._latlng)) {
						c._group._featureGroup.removeLayer(m);
						if (m.clusterShow) {
							m.clusterShow();
						}
					}
				}
			}
		);
	},

	//Run the given functions recursively to this and child clusters
	// boundsToApplyTo: a L.LatLngBounds representing the bounds of what clusters to recurse in to
	// zoomLevelToStart: zoom level to start running functions (inclusive)
	// zoomLevelToStop: zoom level to stop running functions (inclusive)
	// runAtEveryLevel: function that takes an L.MarkerCluster as an argument that should be applied on every level
	// runAtBottomLevel: function that takes an L.MarkerCluster as an argument that should be applied at only the bottom level
	_recursively: function (boundsToApplyTo, zoomLevelToStart, zoomLevelToStop, runAtEveryLevel, runAtBottomLevel) {
		var childClusters = this._childClusters,
		    zoom = this._zoom,
		    i, c;

		if (zoomLevelToStart <= zoom) {
			if (runAtEveryLevel) {
				runAtEveryLevel(this);
			}
			if (runAtBottomLevel && zoom === zoomLevelToStop) {
				runAtBottomLevel(this);
			}
		}

		if (zoom < zoomLevelToStart || zoom < zoomLevelToStop) {
			for (i = childClusters.length - 1; i >= 0; i--) {
				c = childClusters[i];
				if (boundsToApplyTo.intersects(c._bounds)) {
					c._recursively(boundsToApplyTo, zoomLevelToStart, zoomLevelToStop, runAtEveryLevel, runAtBottomLevel);
				}
			}
		}
	},

	//Returns true if we are the parent of only one cluster and that cluster is the same as us
	_isSingleParent: function () {
		//Don't need to check this._markers as the rest won't work if there are any
		return this._childClusters.length > 0 && this._childClusters[0]._childCount === this._childCount;
	}
});



/*
* Extends L.Marker to include two extra methods: clusterHide and clusterShow.
*
* They work as setOpacity(0) and setOpacity(1) respectively, but
* they will remember the marker's opacity when hiding and showing it again.
*
*/


L.Marker.include({

	clusterHide: function () {
		this.options.opacityWhenUnclustered = this.options.opacity || 1;
		return this.setOpacity(0);
	},

	clusterShow: function () {
		var ret = this.setOpacity(this.options.opacity || this.options.opacityWhenUnclustered);
		delete this.options.opacityWhenUnclustered;
		return ret;
	}

});





L.DistanceGrid = function (cellSize) {
	this._cellSize = cellSize;
	this._sqCellSize = cellSize * cellSize;
	this._grid = {};
	this._objectPoint = { };
};

L.DistanceGrid.prototype = {

	addObject: function (obj, point) {
		var x = this._getCoord(point.x),
		    y = this._getCoord(point.y),
		    grid = this._grid,
		    row = grid[y] = grid[y] || {},
		    cell = row[x] = row[x] || [],
		    stamp = L.Util.stamp(obj);

		this._objectPoint[stamp] = point;

		cell.push(obj);
	},

	updateObject: function (obj, point) {
		this.removeObject(obj);
		this.addObject(obj, point);
	},

	//Returns true if the object was found
	removeObject: function (obj, point) {
		var x = this._getCoord(point.x),
		    y = this._getCoord(point.y),
		    grid = this._grid,
		    row = grid[y] = grid[y] || {},
		    cell = row[x] = row[x] || [],
		    i, len;

		delete this._objectPoint[L.Util.stamp(obj)];

		for (i = 0, len = cell.length; i < len; i++) {
			if (cell[i] === obj) {

				cell.splice(i, 1);

				if (len === 1) {
					delete row[x];
				}

				return true;
			}
		}

	},

	eachObject: function (fn, context) {
		var i, j, k, len, row, cell, removed,
		    grid = this._grid;

		for (i in grid) {
			row = grid[i];

			for (j in row) {
				cell = row[j];

				for (k = 0, len = cell.length; k < len; k++) {
					removed = fn.call(context, cell[k]);
					if (removed) {
						k--;
						len--;
					}
				}
			}
		}
	},

	getNearObject: function (point) {
		var x = this._getCoord(point.x),
		    y = this._getCoord(point.y),
		    i, j, k, row, cell, len, obj, dist,
		    objectPoint = this._objectPoint,
		    closestDistSq = this._sqCellSize,
		    closest = null;

		for (i = y - 1; i <= y + 1; i++) {
			row = this._grid[i];
			if (row) {

				for (j = x - 1; j <= x + 1; j++) {
					cell = row[j];
					if (cell) {

						for (k = 0, len = cell.length; k < len; k++) {
							obj = cell[k];
							dist = this._sqDist(objectPoint[L.Util.stamp(obj)], point);
							if (dist < closestDistSq) {
								closestDistSq = dist;
								closest = obj;
							}
						}
					}
				}
			}
		}
		return closest;
	},

	_getCoord: function (x) {
		return Math.floor(x / this._cellSize);
	},

	_sqDist: function (p, p2) {
		var dx = p2.x - p.x,
		    dy = p2.y - p.y;
		return dx * dx + dy * dy;
	}
};


/* Copyright (c) 2012 the authors listed at the following URL, and/or
the authors of referenced articles or incorporated external code:
http://en.literateprograms.org/Quickhull_(Javascript)?action=history&offset=20120410175256

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Retrieved from: http://en.literateprograms.org/Quickhull_(Javascript)?oldid=18434
*/

(function () {
	L.QuickHull = {

		/*
		 * @param {Object} cpt a point to be measured from the baseline
		 * @param {Array} bl the baseline, as represented by a two-element
		 *   array of latlng objects.
		 * @returns {Number} an approximate distance measure
		 */
		getDistant: function (cpt, bl) {
			var vY = bl[1].lat - bl[0].lat,
				vX = bl[0].lng - bl[1].lng;
			return (vX * (cpt.lat - bl[0].lat) + vY * (cpt.lng - bl[0].lng));
		},

		/*
		 * @param {Array} baseLine a two-element array of latlng objects
		 *   representing the baseline to project from
		 * @param {Array} latLngs an array of latlng objects
		 * @returns {Object} the maximum point and all new points to stay
		 *   in consideration for the hull.
		 */
		findMostDistantPointFromBaseLine: function (baseLine, latLngs) {
			var maxD = 0,
				maxPt = null,
				newPoints = [],
				i, pt, d;

			for (i = latLngs.length - 1; i >= 0; i--) {
				pt = latLngs[i];
				d = this.getDistant(pt, baseLine);

				if (d > 0) {
					newPoints.push(pt);
				} else {
					continue;
				}

				if (d > maxD) {
					maxD = d;
					maxPt = pt;
				}
			}

			return { maxPoint: maxPt, newPoints: newPoints };
		},


		/*
		 * Given a baseline, compute the convex hull of latLngs as an array
		 * of latLngs.
		 *
		 * @param {Array} latLngs
		 * @returns {Array}
		 */
		buildConvexHull: function (baseLine, latLngs) {
			var convexHullBaseLines = [],
				t = this.findMostDistantPointFromBaseLine(baseLine, latLngs);

			if (t.maxPoint) { // if there is still a point "outside" the base line
				convexHullBaseLines =
					convexHullBaseLines.concat(
						this.buildConvexHull([baseLine[0], t.maxPoint], t.newPoints)
					);
				convexHullBaseLines =
					convexHullBaseLines.concat(
						this.buildConvexHull([t.maxPoint, baseLine[1]], t.newPoints)
					);
				return convexHullBaseLines;
			} else {  // if there is no more point "outside" the base line, the current base line is part of the convex hull
				return [baseLine[0]];
			}
		},

		/*
		 * Given an array of latlngs, compute a convex hull as an array
		 * of latlngs
		 *
		 * @param {Array} latLngs
		 * @returns {Array}
		 */
		getConvexHull: function (latLngs) {
			// find first baseline
			var maxLat = false, minLat = false,
				maxLng = false, minLng = false,
				maxLatPt = null, minLatPt = null,
				maxLngPt = null, minLngPt = null,
				maxPt = null, minPt = null,
				i;

			for (i = latLngs.length - 1; i >= 0; i--) {
				var pt = latLngs[i];
				if (maxLat === false || pt.lat > maxLat) {
					maxLatPt = pt;
					maxLat = pt.lat;
				}
				if (minLat === false || pt.lat < minLat) {
					minLatPt = pt;
					minLat = pt.lat;
				}
				if (maxLng === false || pt.lng > maxLng) {
					maxLngPt = pt;
					maxLng = pt.lng;
				}
				if (minLng === false || pt.lng < minLng) {
					minLngPt = pt;
					minLng = pt.lng;
				}
			}

			if (minLat !== maxLat) {
				minPt = minLatPt;
				maxPt = maxLatPt;
			} else {
				minPt = minLngPt;
				maxPt = maxLngPt;
			}

			var ch = [].concat(this.buildConvexHull([minPt, maxPt], latLngs),
								this.buildConvexHull([maxPt, minPt], latLngs));
			return ch;
		}
	};
}());

L.MarkerCluster.include({
	getConvexHull: function () {
		var childMarkers = this.getAllChildMarkers(),
			points = [],
			p, i;

		for (i = childMarkers.length - 1; i >= 0; i--) {
			p = childMarkers[i].getLatLng();
			points.push(p);
		}

		return L.QuickHull.getConvexHull(points);
	}
});


//This code is 100% based on https://github.com/jawj/OverlappingMarkerSpiderfier-Leaflet
//Huge thanks to jawj for implementing it first to make my job easy :-)

L.MarkerCluster.include({

	_2PI: Math.PI * 2,
	_circleFootSeparation: 25, //related to circumference of circle
	_circleStartAngle: Math.PI / 6,

	_spiralFootSeparation:  28, //related to size of spiral (experiment!)
	_spiralLengthStart: 11,
	_spiralLengthFactor: 5,

	_circleSpiralSwitchover: 9, //show spiral instead of circle from this marker count upwards.
								// 0 -> always spiral; Infinity -> always circle

	spiderfy: function () {
		if (this._group._spiderfied === this || this._group._inZoomAnimation) {
			return;
		}

		var childMarkers = this.getAllChildMarkers(),
			group = this._group,
			map = group._map,
			center = map.latLngToLayerPoint(this._latlng),
			positions;

		this._group._unspiderfy();
		this._group._spiderfied = this;

		//TODO Maybe: childMarkers order by distance to center

		if (childMarkers.length >= this._circleSpiralSwitchover) {
			positions = this._generatePointsSpiral(childMarkers.length, center);
		} else {
			center.y += 10; // Otherwise circles look wrong => hack for standard blue icon, renders differently for other icons.
			positions = this._generatePointsCircle(childMarkers.length, center);
		}

		this._animationSpiderfy(childMarkers, positions);
	},

	unspiderfy: function (zoomDetails) {
		/// <param Name="zoomDetails">Argument from zoomanim if being called in a zoom animation or null otherwise</param>
		if (this._group._inZoomAnimation) {
			return;
		}
		this._animationUnspiderfy(zoomDetails);

		this._group._spiderfied = null;
	},

	_generatePointsCircle: function (count, centerPt) {
		var circumference = this._group.options.spiderfyDistanceMultiplier * this._circleFootSeparation * (2 + count),
			legLength = circumference / this._2PI,  //radius from circumference
			angleStep = this._2PI / count,
			res = [],
			i, angle;

		res.length = count;

		for (i = count - 1; i >= 0; i--) {
			angle = this._circleStartAngle + i * angleStep;
			res[i] = new L.Point(centerPt.x + legLength * Math.cos(angle), centerPt.y + legLength * Math.sin(angle))._round();
		}

		return res;
	},

	_generatePointsSpiral: function (count, centerPt) {
		var spiderfyDistanceMultiplier = this._group.options.spiderfyDistanceMultiplier,
			legLength = spiderfyDistanceMultiplier * this._spiralLengthStart,
			separation = spiderfyDistanceMultiplier * this._spiralFootSeparation,
			lengthFactor = spiderfyDistanceMultiplier * this._spiralLengthFactor * this._2PI,
			angle = 0,
			res = [],
			i;

		res.length = count;

		// Higher index, closer position to cluster center.
		for (i = count - 1; i >= 0; i--) {
			angle += separation / legLength + i * 0.0005;
			res[i] = new L.Point(centerPt.x + legLength * Math.cos(angle), centerPt.y + legLength * Math.sin(angle))._round();
			legLength += lengthFactor / angle;
		}
		return res;
	},

	_noanimationUnspiderfy: function () {
		var group = this._group,
			map = group._map,
			fg = group._featureGroup,
			childMarkers = this.getAllChildMarkers(),
			m, i;

		group._ignoreMove = true;

		this.setOpacity(1);
		for (i = childMarkers.length - 1; i >= 0; i--) {
			m = childMarkers[i];

			fg.removeLayer(m);

			if (m._preSpiderfyLatlng) {
				m.setLatLng(m._preSpiderfyLatlng);
				delete m._preSpiderfyLatlng;
			}
			if (m.setZIndexOffset) {
				m.setZIndexOffset(0);
			}

			if (m._spiderLeg) {
				map.removeLayer(m._spiderLeg);
				delete m._spiderLeg;
			}
		}

		group.fire('unspiderfied', {
			cluster: this,
			markers: childMarkers
		});
		group._ignoreMove = false;
		group._spiderfied = null;
	}
});

//Non Animated versions of everything
L.MarkerClusterNonAnimated = L.MarkerCluster.extend({
	_animationSpiderfy: function (childMarkers, positions) {
		var group = this._group,
			map = group._map,
			fg = group._featureGroup,
			legOptions = this._group.options.spiderLegPolylineOptions,
			i, m, leg, newPos;

		group._ignoreMove = true;

		// Traverse in ascending order to make sure that inner circleMarkers are on top of further legs. Normal markers are re-ordered by newPosition.
		// The reverse order trick no longer improves performance on modern browsers.
		for (i = 0; i < childMarkers.length; i++) {
			newPos = map.layerPointToLatLng(positions[i]);
			m = childMarkers[i];

			// Add the leg before the marker, so that in case the latter is a circleMarker, the leg is behind it.
			leg = new L.Polyline([this._latlng, newPos], legOptions);
			map.addLayer(leg);
			m._spiderLeg = leg;

			// Now add the marker.
			m._preSpiderfyLatlng = m._latlng;
			m.setLatLng(newPos);
			if (m.setZIndexOffset) {
				m.setZIndexOffset(1000000); //Make these appear on top of EVERYTHING
			}

			fg.addLayer(m);
		}
		this.setOpacity(0.3);

		group._ignoreMove = false;
		group.fire('spiderfied', {
			cluster: this,
			markers: childMarkers
		});
	},

	_animationUnspiderfy: function () {
		this._noanimationUnspiderfy();
	}
});

//Animated versions here
L.MarkerCluster.include({

	_animationSpiderfy: function (childMarkers, positions) {
		var me = this,
			group = this._group,
			map = group._map,
			fg = group._featureGroup,
			thisLayerLatLng = this._latlng,
			thisLayerPos = map.latLngToLayerPoint(thisLayerLatLng),
			svg = L.Path.SVG,
			legOptions = L.extend({}, this._group.options.spiderLegPolylineOptions), // Copy the options so that we can modify them for animation.
			finalLegOpacity = legOptions.opacity,
			i, m, leg, legPath, legLength, newPos;

		if (finalLegOpacity === undefined) {
			finalLegOpacity = L.MarkerClusterGroup.prototype.options.spiderLegPolylineOptions.opacity;
		}

		if (svg) {
			// If the initial opacity of the spider leg is not 0 then it appears before the animation starts.
			legOptions.opacity = 0;

			// Add the class for CSS transitions.
			legOptions.className = (legOptions.className || '') + ' leaflet-cluster-spider-leg';
		} else {
			// Make sure we have a defined opacity.
			legOptions.opacity = finalLegOpacity;
		}

		group._ignoreMove = true;

		// Add markers and spider legs to map, hidden at our center point.
		// Traverse in ascending order to make sure that inner circleMarkers are on top of further legs. Normal markers are re-ordered by newPosition.
		// The reverse order trick no longer improves performance on modern browsers.
		for (i = 0; i < childMarkers.length; i++) {
			m = childMarkers[i];

			newPos = map.layerPointToLatLng(positions[i]);

			// Add the leg before the marker, so that in case the latter is a circleMarker, the leg is behind it.
			leg = new L.Polyline([thisLayerLatLng, newPos], legOptions);
			map.addLayer(leg);
			m._spiderLeg = leg;

			// Explanations: https://jakearchibald.com/2013/animated-line-drawing-svg/
			// In our case the transition property is declared in the CSS file.
			if (svg) {
				legPath = leg._path;
				legLength = legPath.getTotalLength() + 0.1; // Need a small extra length to avoid remaining dot in Firefox.
				legPath.style.strokeDasharray = legLength; // Just 1 length is enough, it will be duplicated.
				legPath.style.strokeDashoffset = legLength;
			}

			// If it is a marker, add it now and we'll animate it out
			if (m.setZIndexOffset) {
				m.setZIndexOffset(1000000); // Make normal markers appear on top of EVERYTHING
			}
			if (m.clusterHide) {
				m.clusterHide();
			}

			// Vectors just get immediately added
			fg.addLayer(m);

			if (m._setPos) {
				m._setPos(thisLayerPos);
			}
		}

		group._forceLayout();
		group._animationStart();

		// Reveal markers and spider legs.
		for (i = childMarkers.length - 1; i >= 0; i--) {
			newPos = map.layerPointToLatLng(positions[i]);
			m = childMarkers[i];

			//Move marker to new position
			m._preSpiderfyLatlng = m._latlng;
			m.setLatLng(newPos);

			if (m.clusterShow) {
				m.clusterShow();
			}

			// Animate leg (animation is actually delegated to CSS transition).
			if (svg) {
				leg = m._spiderLeg;
				legPath = leg._path;
				legPath.style.strokeDashoffset = 0;
				//legPath.style.strokeOpacity = finalLegOpacity;
				leg.setStyle({opacity: finalLegOpacity});
			}
		}
		this.setOpacity(0.3);

		group._ignoreMove = false;

		setTimeout(function () {
			group._animationEnd();
			group.fire('spiderfied', {
				cluster: me,
				markers: childMarkers
			});
		}, 200);
	},

	_animationUnspiderfy: function (zoomDetails) {
		var me = this,
			group = this._group,
			map = group._map,
			fg = group._featureGroup,
			thisLayerPos = zoomDetails ? map._latLngToNewLayerPoint(this._latlng, zoomDetails.zoom, zoomDetails.center) : map.latLngToLayerPoint(this._latlng),
			childMarkers = this.getAllChildMarkers(),
			svg = L.Path.SVG,
			m, i, leg, legPath, legLength, nonAnimatable;

		group._ignoreMove = true;
		group._animationStart();

		//Make us visible and bring the child markers back in
		this.setOpacity(1);
		for (i = childMarkers.length - 1; i >= 0; i--) {
			m = childMarkers[i];

			//Marker was added to us after we were spiderfied
			if (!m._preSpiderfyLatlng) {
				continue;
			}

			//Close any popup on the marker first, otherwise setting the location of the marker will make the map scroll
			m.closePopup();

			//Fix up the location to the real one
			m.setLatLng(m._preSpiderfyLatlng);
			delete m._preSpiderfyLatlng;

			//Hack override the location to be our center
			nonAnimatable = true;
			if (m._setPos) {
				m._setPos(thisLayerPos);
				nonAnimatable = false;
			}
			if (m.clusterHide) {
				m.clusterHide();
				nonAnimatable = false;
			}
			if (nonAnimatable) {
				fg.removeLayer(m);
			}

			// Animate the spider leg back in (animation is actually delegated to CSS transition).
			if (svg) {
				leg = m._spiderLeg;
				legPath = leg._path;
				legLength = legPath.getTotalLength() + 0.1;
				legPath.style.strokeDashoffset = legLength;
				leg.setStyle({opacity: 0});
			}
		}

		group._ignoreMove = false;

		setTimeout(function () {
			//If we have only <= one child left then that marker will be shown on the map so don't remove it!
			var stillThereChildCount = 0;
			for (i = childMarkers.length - 1; i >= 0; i--) {
				m = childMarkers[i];
				if (m._spiderLeg) {
					stillThereChildCount++;
				}
			}


			for (i = childMarkers.length - 1; i >= 0; i--) {
				m = childMarkers[i];

				if (!m._spiderLeg) { //Has already been unspiderfied
					continue;
				}

				if (m.clusterShow) {
					m.clusterShow();
				}
				if (m.setZIndexOffset) {
					m.setZIndexOffset(0);
				}

				if (stillThereChildCount > 1) {
					fg.removeLayer(m);
				}

				map.removeLayer(m._spiderLeg);
				delete m._spiderLeg;
			}
			group._animationEnd();
			group.fire('unspiderfied', {
				cluster: me,
				markers: childMarkers
			});
		}, 200);
	}
});


L.MarkerClusterGroup.include({
	//The MarkerCluster currently spiderfied (if any)
	_spiderfied: null,

	unspiderfy: function () {
		this._unspiderfy.apply(this, arguments);
	},

	_spiderfierOnAdd: function () {
		this._map.on('click', this._unspiderfyWrapper, this);

		if (this._map.options.zoomAnimation) {
			this._map.on('zoomstart', this._unspiderfyZoomStart, this);
		}
		//Browsers without zoomAnimation or a big zoom don't fire zoomstart
		this._map.on('zoomend', this._noanimationUnspiderfy, this);

		if (!L.Browser.touch) {
			this._map.getRenderer(this);
			//Needs to happen in the pageload, not after, or animations don't work in webkit
			//  http://stackoverflow.com/questions/8455200/svg-animate-with-dynamically-added-elements
			//Disable on touch browsers as the animation messes up on a touch zoom and isn't very noticable
		}
	},

	_spiderfierOnRemove: function () {
		this._map.off('click', this._unspiderfyWrapper, this);
		this._map.off('zoomstart', this._unspiderfyZoomStart, this);
		this._map.off('zoomanim', this._unspiderfyZoomAnim, this);
		this._map.off('zoomend', this._noanimationUnspiderfy, this);

		//Ensure that markers are back where they should be
		// Use no animation to avoid a sticky leaflet-cluster-anim class on mapPane
		this._noanimationUnspiderfy();
	},

	//On zoom start we add a zoomanim handler so that we are guaranteed to be last (after markers are animated)
	//This means we can define the animation they do rather than Markers doing an animation to their actual location
	_unspiderfyZoomStart: function () {
		if (!this._map) { //May have been removed from the map by a zoomEnd handler
			return;
		}

		this._map.on('zoomanim', this._unspiderfyZoomAnim, this);
	},

	_unspiderfyZoomAnim: function (zoomDetails) {
		//Wait until the first zoomanim after the user has finished touch-zooming before running the animation
		if (L.DomUtil.hasClass(this._map._mapPane, 'leaflet-touching')) {
			return;
		}

		this._map.off('zoomanim', this._unspiderfyZoomAnim, this);
		this._unspiderfy(zoomDetails);
	},

	_unspiderfyWrapper: function () {
		/// <summary>_unspiderfy but passes no arguments</summary>
		this._unspiderfy();
	},

	_unspiderfy: function (zoomDetails) {
		if (this._spiderfied) {
			this._spiderfied.unspiderfy(zoomDetails);
		}
	},

	_noanimationUnspiderfy: function () {
		if (this._spiderfied) {
			this._spiderfied._noanimationUnspiderfy();
		}
	},

	//If the given layer is currently being spiderfied then we unspiderfy it so it isn't on the map anymore etc
	_unspiderfyLayer: function (layer) {
		if (layer._spiderLeg) {
			this._featureGroup.removeLayer(layer);

			if (layer.clusterShow) {
				layer.clusterShow();
			}
				//Position will be fixed up immediately in _animationUnspiderfy
			if (layer.setZIndexOffset) {
				layer.setZIndexOffset(0);
			}

			this._map.removeLayer(layer._spiderLeg);
			delete layer._spiderLeg;
		}
	}
});


/**
 * Adds 1 public method to MCG and 1 to L.Marker to facilitate changing
 * markers' icon options and refreshing their icon and their parent clusters
 * accordingly (case where their iconCreateFunction uses data of childMarkers
 * to make up the cluster icon).
 */


L.MarkerClusterGroup.include({
	/**
	 * Updates the icon of all clusters which are parents of the given marker(s).
	 * In singleMarkerMode, also updates the given marker(s) icon.
	 * @param layers L.MarkerClusterGroup|L.LayerGroup|Array(L.Marker)|Map(L.Marker)|
	 * L.MarkerCluster|L.Marker (optional) list of markers (or single marker) whose parent
	 * clusters need to be updated. If not provided, retrieves all child markers of this.
	 * @returns {L.MarkerClusterGroup}
	 */
	refreshClusters: function (layers) {
		if (!layers) {
			layers = this._topClusterLevel.getAllChildMarkers();
		} else if (layers instanceof L.MarkerClusterGroup) {
			layers = layers._topClusterLevel.getAllChildMarkers();
		} else if (layers instanceof L.LayerGroup) {
			layers = layers._layers;
		} else if (layers instanceof L.MarkerCluster) {
			layers = layers.getAllChildMarkers();
		} else if (layers instanceof L.Marker) {
			layers = [layers];
		} // else: must be an Array(L.Marker)|Map(L.Marker)
		this._flagParentsIconsNeedUpdate(layers);
		this._refreshClustersIcons();

		// In case of singleMarkerMode, also re-draw the markers.
		if (this.options.singleMarkerMode) {
			this._refreshSingleMarkerModeMarkers(layers);
		}

		return this;
	},

	/**
	 * Simply flags all parent clusters of the given markers as having a "dirty" icon.
	 * @param layers Array(L.Marker)|Map(L.Marker) list of markers.
	 * @private
	 */
	_flagParentsIconsNeedUpdate: function (layers) {
		var id, parent;

		// Assumes layers is an Array or an Object whose prototype is non-enumerable.
		for (id in layers) {
			// Flag parent clusters' icon as "dirty", all the way up.
			// Dumb process that flags multiple times upper parents, but still
			// much more efficient than trying to be smart and make short lists,
			// at least in the case of a hierarchy following a power law:
			// http://jsperf.com/flag-nodes-in-power-hierarchy/2
			parent = layers[id].__parent;
			while (parent) {
				parent._iconNeedsUpdate = true;
				parent = parent.__parent;
			}
		}
	},

	/**
	 * Re-draws the icon of the supplied markers.
	 * To be used in singleMarkerMode only.
	 * @param layers Array(L.Marker)|Map(L.Marker) list of markers.
	 * @private
	 */
	_refreshSingleMarkerModeMarkers: function (layers) {
		var id, layer;

		for (id in layers) {
			layer = layers[id];

			// Make sure we do not override markers that do not belong to THIS group.
			if (this.hasLayer(layer)) {
				// Need to re-create the icon first, then re-draw the marker.
				layer.setIcon(this._overrideMarkerIcon(layer));
			}
		}
	}
});

L.Marker.include({
	/**
	 * Updates the given options in the marker's icon and refreshes the marker.
	 * @param options map object of icon options.
	 * @param directlyRefreshClusters boolean (optional) true to trigger
	 * MCG.refreshClustersOf() right away with this single marker.
	 * @returns {L.Marker}
	 */
	refreshIconOptions: function (options, directlyRefreshClusters) {
		var icon = this.options.icon;

		L.setOptions(icon, options);

		this.setIcon(icon);

		// Shortcut to refresh the associated MCG clusters right away.
		// To be used when refreshing a single marker.
		// Otherwise, better use MCG.refreshClusters() once at the end with
		// the list of modified markers.
		if (directlyRefreshClusters && this.__parent) {
			this.__parent._group.refreshClusters(this);
		}

		return this;
	}
});


}(window, document));

/*
* MiniMap plugin by Norkart, https://github.com/Norkart/Leaflet-MiniMap
* Last commits included: 06/10/2016 ( 3.4.0 ) - custom code beachten (mit RH gekennzeichnet)!
*/
// Following https://github.com/Leaflet/Leaflet/blob/master/PLUGIN-GUIDE.md
(function (factory, window) {

	// define an AMD module that relies on 'leaflet'
	if (typeof define === 'function' && define.amd) {
		define(['leaflet'], factory);

	// define a Common JS module that relies on 'leaflet'
	} else if (typeof exports === 'object') {
		module.exports = factory(require('leaflet'));
	}

	// attach your plugin to the global 'L' variable
	if (typeof window !== 'undefined' && window.L) {
		window.L.Control.MiniMap = factory(L);
		window.L.control.minimap = function (layer, options) {
			return new window.L.Control.MiniMap(layer, options);
		};
	}
}(function (L) {

	var MiniMap = L.Control.extend({
		options: {
			position: 'bottomright',
			toggleDisplay: false,
			zoomLevelOffset: -5,
			zoomLevelFixed: false,
			centerFixed: false,
			zoomAnimation: false,
			autoToggleDisplay: false,
			minimized: false,
			width: 150,
			height: 150,
			collapsedWidth: 19,
			collapsedHeight: 19,
			aimingRectOptions: {color: "#ff7800", weight: 1, interactive: false},
			shadowRectOptions: {color: "#000000", weight: 1, interactive: false, opacity: 0, fillOpacity: 0},
			strings: {hideText: mapsmarkerjspro.minimap_hide, showText: mapsmarkerjspro.minimap_show},
			mapOptions: {}  // Allows definition / override of Leaflet map options.
		},

		// layer is the map layer to be shown in the minimap
		initialize: function (layer, options) {
			L.Util.setOptions(this, options);
			// Make sure the aiming rects are non-clickable even if the user tries to set them clickable (most likely by forgetting to specify them false)
			this.options.aimingRectOptions.interactive = false;
			this.options.shadowRectOptions.interactive = false;
			this._layer = layer;
		},

		onAdd: function (map) {

			this._mainMap = map;

			// Creating the container and stopping events from spilling through to the main map.
			this._container = L.DomUtil.create('div', 'leaflet-control-minimap');
			this._container.style.width = this.options.width + 'px';
			this._container.style.height = this.options.height + 'px';
			L.DomEvent.disableClickPropagation(this._container);
			L.DomEvent.on(this._container, 'mousewheel', L.DomEvent.stopPropagation);

			var mapOptions = {
				attributionControl: false,
				dragging: !this.options.centerFixed,
				zoomControl: false,
				zoomAnimation: this.options.zoomAnimation,
				autoToggleDisplay: this.options.autoToggleDisplay,
				touchZoom: this.options.centerFixed ? 'center' : !this._isZoomLevelFixed(),
				scrollWheelZoom: this.options.centerFixed ? 'center' : !this._isZoomLevelFixed(),
				doubleClickZoom: this.options.centerFixed ? 'center' : !this._isZoomLevelFixed(),
				boxZoom: !this._isZoomLevelFixed(),
				crs: map.options.crs
			};
			mapOptions = L.Util.extend(this.options.mapOptions, mapOptions);  // merge with priority of the local mapOptions object.

			this._miniMap = new L.Map(this._container, mapOptions);

			this._miniMap.addLayer(this._layer);

			// These bools are used to prevent infinite loops of the two maps notifying each other that they've moved.
			this._mainMapMoving = false;
			this._miniMapMoving = false;

			// Keep a record of this to prevent auto toggling when the user explicitly doesn't want it.
			this._userToggledDisplay = false;
			this._minimized = false;

			if (this.options.toggleDisplay) {
				this._addToggleButton();
			}

			this._miniMap.whenReady(L.Util.bind(function () {
				this._aimingRect = L.rectangle(this._mainMap.getBounds(), this.options.aimingRectOptions).addTo(this._miniMap);
				this._shadowRect = L.rectangle(this._mainMap.getBounds(), this.options.shadowRectOptions).addTo(this._miniMap);
				this._mainMap.on('moveend', this._onMainMapMoved, this);
				this._mainMap.on('move', this._onMainMapMoving, this);
				this._miniMap.on('movestart', this._onMiniMapMoveStarted, this);
				this._miniMap.on('move', this._onMiniMapMoving, this);
				this._miniMap.on('moveend', this._onMiniMapMoved, this);
			}, this));

			return this._container;
		},

		addTo: function (map) {
			L.Control.prototype.addTo.call(this, map);

			var center = this.options.centerFixed || this._mainMap.getCenter();
			this._miniMap.setView(center, this._decideZoom(true));
			this._setDisplay(this.options.minimized);
			return this;
		},

		onRemove: function (map) {
			this._mainMap.off('moveend', this._onMainMapMoved, this);
			this._mainMap.off('move', this._onMainMapMoving, this);
			this._miniMap.off('moveend', this._onMiniMapMoved, this);

			this._miniMap.removeLayer(this._layer);
		},

		changeLayer: function (layer) {
			this._miniMap.removeLayer(this._layer);
			this._layer = layer;
			this._miniMap.addLayer(this._layer);
		},

		_addToggleButton: function () {
			this._toggleDisplayButton = this.options.toggleDisplay ? this._createButton(
				'', this._toggleButtonInitialTitleText(), ('leaflet-control-minimap-toggle-display leaflet-control-minimap-toggle-display-' +
				this.options.position), this._container, this._toggleDisplayButtonClicked, this) : undefined;

			this._toggleDisplayButton.style.width = this.options.collapsedWidth + 'px';
			this._toggleDisplayButton.style.height = this.options.collapsedHeight + 'px';
		},

		_toggleButtonInitialTitleText: function () {
			if (this.options.minimized) {
				return this.options.strings.showText;
			} else {
				return this.options.strings.hideText
			}
		},

		_createButton: function (html, title, className, container, fn, context) {
			var link = L.DomUtil.create('a', className, container);
			link.innerHTML = html;
			link.href = '#';
			link.title = title;

			var stop = L.DomEvent.stopPropagation;

			L.DomEvent
				.on(link, 'click', stop)
				.on(link, 'mousedown', stop)
				.on(link, 'dblclick', stop)
				.on(link, 'click', L.DomEvent.preventDefault)
				.on(link, 'click', fn, context);

			return link;
		},

		_toggleDisplayButtonClicked: function () {
			this._userToggledDisplay = true;
			if (!this._minimized) {
				this._minimize();
			} else {
				this._restore();
			}
		},

		_setDisplay: function (minimize) {
			if (minimize !== this._minimized) {
				if (!this._minimized) {
					this._minimize();
				} else {
					this._restore();
				}
			}
		},

		_minimize: function () {
			// hide the minimap
			if (this.options.toggleDisplay) {
				this._container.style.width = this.options.collapsedWidth + 'px';
				this._container.style.height = this.options.collapsedHeight + 'px';
				this._toggleDisplayButton.className += (' minimized-' + this.options.position);
				this._toggleDisplayButton.title = this.options.strings.showText;
			} else {
				this._container.style.display = 'none';
			}
			this._minimized = true;
		},

		_restore: function () {
			if (this.options.toggleDisplay) {
				this._container.style.width = this.options.width + 'px';
				this._container.style.height = this.options.height + 'px';
				this._toggleDisplayButton.className = this._toggleDisplayButton.className
					.replace('minimized-'	+ this.options.position, '');
				this._toggleDisplayButton.title = this.options.strings.hideText;
			} else {
				this._container.style.display = 'block';
			}
			this._minimized = false;
		},

		_onMainMapMoved: function (e) {
			if (!this._miniMapMoving) {
				var center = this.options.centerFixed || this._mainMap.getCenter();

				this._mainMapMoving = true;
				this._miniMap.setView(center, this._decideZoom(true));
				this._setDisplay(this._decideMinimized());
			} else {
				this._miniMapMoving = false;
			}
			this._aimingRect.setBounds(this._mainMap.getBounds());
		},

		_onMainMapMoving: function (e) {
			this._aimingRect.setBounds(this._mainMap.getBounds());
		},

		_onMiniMapMoveStarted: function (e) {
			if (!this.options.centerFixed) {
				var lastAimingRect = this._aimingRect.getBounds();
				var sw = this._miniMap.latLngToContainerPoint(lastAimingRect.getSouthWest());
				var ne = this._miniMap.latLngToContainerPoint(lastAimingRect.getNorthEast());
				this._lastAimingRectPosition = {sw: sw, ne: ne};
			}
		},

		_onMiniMapMoving: function (e) {
			if (!this.options.centerFixed) {
				if (!this._mainMapMoving && this._lastAimingRectPosition) {
					this._shadowRect.setBounds(new L.LatLngBounds(this._miniMap.containerPointToLatLng(this._lastAimingRectPosition.sw), this._miniMap.containerPointToLatLng(this._lastAimingRectPosition.ne)));
					this._shadowRect.setStyle({opacity: 1, fillOpacity: 0.3});
				}
			}
		},

		_onMiniMapMoved: function (e) {
			if (!this._mainMapMoving) {
				this._miniMapMoving = true;
				this._mainMap.setView(this._miniMap.getCenter(), this._decideZoom(false));
				this._shadowRect.setStyle({opacity: 0, fillOpacity: 0});
			} else {
				this._mainMapMoving = false;
			}
		},

		_isZoomLevelFixed: function () {
			var zoomLevelFixed = this.options.zoomLevelFixed;
			return this._isDefined(zoomLevelFixed) && this._isInteger(zoomLevelFixed);
		},

		_decideZoom: function (fromMaintoMini) {
			if (!this._isZoomLevelFixed()) {
				if (fromMaintoMini) {
					return this._mainMap.getZoom() + this.options.zoomLevelOffset;
				} else {
					var currentDiff = this._miniMap.getZoom() - this._mainMap.getZoom();
					var proposedZoom = this._miniMap.getZoom() - this.options.zoomLevelOffset;
					var toRet;

					if (currentDiff > this.options.zoomLevelOffset && this._mainMap.getZoom() < this._miniMap.getMinZoom() - this.options.zoomLevelOffset) {
						// This means the miniMap is zoomed out to the minimum zoom level and can't zoom any more.
						if (this._miniMap.getZoom() > this._lastMiniMapZoom) {
							// This means the user is trying to zoom in by using the minimap, zoom the main map.
							toRet = this._mainMap.getZoom() + 1;
							// Also we cheat and zoom the minimap out again to keep it visually consistent.
							this._miniMap.setZoom(this._miniMap.getZoom() - 1);
						} else {
							// Either the user is trying to zoom out past the mini map's min zoom or has just panned using it, we can't tell the difference.
							// Therefore, we ignore it!
							toRet = this._mainMap.getZoom();
						}
					} else {
						// This is what happens in the majority of cases, and always if you configure the min levels + offset in a sane fashion.
						toRet = proposedZoom;
					}
					this._lastMiniMapZoom = this._miniMap.getZoom();
					return toRet;
				}
			} else {
				if (fromMaintoMini) {
					return this.options.zoomLevelFixed;
				} else {
					return this._mainMap.getZoom();
				}
			}
		},

		_decideMinimized: function () {
			if (this._userToggledDisplay) {
				return this._minimized;
			}

			if (this.options.autoToggleDisplay) {
				if (this._mainMap.getBounds().contains(this._miniMap.getBounds())) {
					return true;
				}
				return false;
			}

			return this._minimized;
		},

		_isInteger: function (value) {
			return typeof value === 'number';
		},

		_isDefined: function (value) {
			return typeof value !== 'undefined';
		}
	});

	L.Map.mergeOptions({
		miniMapControl: false
	});

	L.Map.addInitHook(function () {
		if (this.options.miniMapControl) {
			this.miniMapControl = (new MiniMap()).addTo(this);
		}
	});

	return MiniMap;

}, window));

/*
* Fullscreen plugin by https://github.com/mapbox/Leaflet.fullscreen
* License: https://github.com/mapbox/Leaflet.fullscreen/blob/master/LICENSE
*/
L.Control.Fullscreen = L.Control.extend({
    options: {
        position: mapsmarkerjspro.fullscreen_button_position,
        title: {
            'false': mapsmarkerjspro.fullscreen_button_title,
			'true': mapsmarkerjspro.fullscreen_button_title_exit
        }
    },

    onAdd: function (map) {
        var container = L.DomUtil.create('div', 'leaflet-control-fullscreen leaflet-bar leaflet-control');

        this.link = L.DomUtil.create('a', 'leaflet-control-fullscreen-button leaflet-bar-part', container);
        this.link.href = '#';

        this._map = map;
        this._map.on('fullscreenchange', this._toggleTitle, this);
        this._toggleTitle();

        L.DomEvent.on(this.link, 'click', this._click, this);

        return container;
    },

    _click: function (e) {
        L.DomEvent.stopPropagation(e);
        L.DomEvent.preventDefault(e);
        this._map.toggleFullscreen(this.options);
    },

    _toggleTitle: function() {
        this.link.title = this.options.title[this._map.isFullscreen()];
    }
});

L.Map.include({
    isFullscreen: function () {
        return this._isFullscreen || false;
    },

    toggleFullscreen: function (options) {
        var container = this.getContainer();
        if (this.isFullscreen()) {
            if (options && options.pseudoFullscreen) {
                this._disablePseudoFullscreen(container);
            } else if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitCancelFullScreen) {
                document.webkitCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else {
                this._disablePseudoFullscreen(container);
            }
        } else {
            if (options && options.pseudoFullscreen) {
                this._enablePseudoFullscreen(container);
            } else if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.mozRequestFullScreen) {
                container.mozRequestFullScreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            } else {
                this._enablePseudoFullscreen(container);
            }
        }

    },

    _enablePseudoFullscreen: function (container) {
        L.DomUtil.addClass(container, 'leaflet-pseudo-fullscreen');
        this._setFullscreen(true);
        this.fire('fullscreenchange');
    },

    _disablePseudoFullscreen: function (container) {
        L.DomUtil.removeClass(container, 'leaflet-pseudo-fullscreen');
        this._setFullscreen(false);
        this.fire('fullscreenchange');
    },

    _setFullscreen: function(fullscreen) {
        this._isFullscreen = fullscreen;
        var container = this.getContainer();
        if (fullscreen) {
            L.DomUtil.addClass(container, 'leaflet-fullscreen-on');
        } else {
            L.DomUtil.removeClass(container, 'leaflet-fullscreen-on');
        }
        this.invalidateSize();
    },

    _onFullscreenChange: function (e) {
        var fullscreenElement =
            document.fullscreenElement ||
            document.mozFullScreenElement ||
            document.webkitFullscreenElement ||
            document.msFullscreenElement;

        if (fullscreenElement === this.getContainer() && !this._isFullscreen) {
            this._setFullscreen(true);
            this.fire('fullscreenchange');
        } else if (fullscreenElement !== this.getContainer() && this._isFullscreen) {
            this._setFullscreen(false);
            this.fire('fullscreenchange');
        }
    }
});

L.Map.mergeOptions({
    fullscreenControl: false
});

L.Map.addInitHook(function () {
    if (this.options.fullscreenControl) {
        this.fullscreenControl = new L.Control.Fullscreen(this.options.fullscreenControl);
        this.addControl(this.fullscreenControl);
    }

    var fullscreenchange;

    if ('onfullscreenchange' in document) {
        fullscreenchange = 'fullscreenchange';
    } else if ('onmozfullscreenchange' in document) {
        fullscreenchange = 'mozfullscreenchange';
    } else if ('onwebkitfullscreenchange' in document) {
        fullscreenchange = 'webkitfullscreenchange';
    } else if ('onmsfullscreenchange' in document) {
        fullscreenchange = 'MSFullscreenChange';
    }

    if (fullscreenchange) {
        var onFullscreenChange = L.bind(this._onFullscreenChange, this);

        this.whenReady(function () {
            L.DomEvent.on(document, fullscreenchange, onFullscreenChange);
        });

        this.on('unload', function () {
            L.DomEvent.off(document, fullscreenchange, onFullscreenChange);
        });
    }
});

L.control.fullscreen = function (options) {
    return new L.Control.Fullscreen(options);
};

/*
* GPX plugin, Copyright (C) 2011-2012 Pavel Shramov, Copyright (C) 2013 Maxime Petazzoni <maxime.petazzoni@bulix.org>
* License: https://github.com/mpetazzoni/leaflet-gpx/blob/master/LICENSE
*/
var _SECOND_IN_MILLIS = 1000;
var _MINUTE_IN_MILLIS = 60 * _SECOND_IN_MILLIS;
var _HOUR_IN_MILLIS = 60 * _MINUTE_IN_MILLIS;
L.GPX = L.FeatureGroup.extend({
	initialize: function(gpx, options) {
		L.Util.setOptions(this, options);
		if (mapsmarkerjspro.gpx_icons_status == 'show') { //info: added by RH
			L.GPXTrackIcon = L.Icon.extend({ options: options.marker_options });
		}
		this._gpx = gpx;
		this._layers = {};
		this._info = {
			name: null,
			length: 0.0,
			elevation: {gain: 0.0, loss: 0.0, _points: []},
			hr: {avg: 0, _total: 0, _points: []},
			duration: {start: null, end: null, moving: 0, total: 0}
		};
		if (gpx) {
			this._prepare_parsing(gpx, options, this.options.async);
		}
	},

	get_duration_string: function(duration, hidems) {
		var s = '';

		if (duration >= _HOUR_IN_MILLIS) {
			s += Math.floor(duration / _HOUR_IN_MILLIS) + ':';
			duration = duration % _HOUR_IN_MILLIS;
		}

		var mins = Math.floor(duration / _MINUTE_IN_MILLIS);
		duration = duration % _MINUTE_IN_MILLIS;
		if (mins < 10) s += '0';
		s += mins + '\'';

		var secs = Math.floor(duration / _SECOND_IN_MILLIS);
		duration = duration % _SECOND_IN_MILLIS;
		if (secs < 10) s += '0';
		s += secs;

		if (!hidems && duration > 0) s += '.' + Math.round(Math.floor(duration)*1000)/1000;
		else s += '"';
		return s;
	},

	to_miles:            function(v) { return v / 1.60934; },
	to_ft:               function(v) { return v * 3.28084; },
	m_to_km:             function(v) { return v / 1000; },
	m_to_mi:             function(v) { return v / 1609.34; },

	get_name:            function() { return this._info.name; },
	get_distance:        function() { return this._info.length; },
	get_distance_imp:    function() { return this.to_miles(this.m_to_km(this.get_distance())); },

	get_start_time:      function() { return this._info.duration.start; },
	get_end_time:        function() { return this._info.duration.end; },
	get_moving_time:     function() { return this._info.duration.moving; },
	get_total_time:      function() { return this._info.duration.total; },

	get_moving_pace:     function() { return this.get_moving_time() / this.m_to_km(this.get_distance()); },
	get_moving_pace_imp: function() { return this.get_moving_time() / this.get_distance_imp(); },

	get_elevation_gain:     function() { return this._info.elevation.gain; },
	get_elevation_loss:     function() { return this._info.elevation.loss; },
	get_elevation_data:     function() {
		var _this = this;
		return this._info.elevation._points.map(
			function(p) {
				return _this._prepare_data_point(p, _this.m_to_km, null,
				function(a, b) {
					return a.toFixed(2) + ' km, ' + b.toFixed(0) + ' m';
				});
			});
	},
	get_elevation_data_imp: function() {
		var _this = this;
		return this._info.elevation._points.map(
			function(p) {
				return _this._prepare_data_point(p, _this.m_to_mi, _this.to_ft,
				function(a, b) {
					return a.toFixed(2) + ' mi, ' + b.toFixed(0) + ' ft';
				});
			});
	},

	get_average_hr:         function() { return this._info.hr.avg; },
	get_heartrate_data:     function() {
		var _this = this;
		return this._info.hr._points.map(
			function(p) {
				return _this._prepare_data_point(p, _this.m_to_km, null,
				function(a, b) {
					return a.toFixed(2) + ' km, ' + b.toFixed(0) + ' bpm';
				});
			});
	},
	get_heartrate_data_imp: function() {
		var _this = this;
		return this._info.hr._points.map(
			function(p) {
				return _this._prepare_data_point(p, _this.m_to_mi, null,
				function(a, b) {
					return a.toFixed(2) + ' mi, ' + b.toFixed(0) + ' bpm';
				});
			});
	},

	//**************************************************************************/
	// Private methods
	//**************************************************************************/

	_htmlspecialchars_decode: function htmlspecialchars_decode (string, quote_style) {
		// http://kevin.vanzonneveld.net
		// *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
		// *     returns 1: '<p>this -> &quot;</p>'
		// *     example 2: htmlspecialchars_decode("&amp;quot;");
		// *     returns 2: '&quot;'
		var optTemp = 0,
		i = 0,
		noquotes = false;
		if (typeof quote_style === 'undefined') {
			quote_style = 2;
		}
		string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
		var OPTS = {
			'ENT_NOQUOTES': 0,
			'ENT_HTML_QUOTE_SINGLE': 1,
			'ENT_HTML_QUOTE_DOUBLE': 2,
			'ENT_COMPAT': 2,
			'ENT_QUOTES': 3,
			'ENT_IGNORE': 4
		};
		if (quote_style === 0) {
			noquotes = true;
		}
		if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
			quote_style = [].concat(quote_style);
			for (i = 0; i < quote_style.length; i++) {
				// Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
				if (OPTS[quote_style[i]] === 0) {
					noquotes = true;
				} else if (OPTS[quote_style[i]]) {
					optTemp = optTemp | OPTS[quote_style[i]];
				}
			}
			quote_style = optTemp;
		}
		if (quote_style && OPTS.ENT_HTML_QUOTE_SINGLE) {
			string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
			// string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
		}
		if (!noquotes) {
			string = string.replace(/&quot;/g, '"');
		}
		// Put this in last place to avoid escape being double-decoded
		string = string.replace(/&amp;/g, '&');

		return string;
	},

	_merge_objs: function(a, b) {
		var _ = {};
		for (var attr in a) { _[attr] = a[attr]; }
		for (var attr in b) { _[attr] = b[attr]; }
		return _;
	},

	_prepare_data_point: function(p, trans1, trans2, trans_tooltip) {
		var r = [trans1 && trans1(p[0]) || p[0], trans2 && trans2(p[1]) || p[1]];
		r.push(trans_tooltip && trans_tooltip(r[0], r[1]) || (r[0] + ': ' + r[1]));
		return r;
	},

	_prepare_parsing: function(input, options, async) {
		var _this = this;
		var cb = function(gpx, options) {
			var layers = _this._parse_gpx_data(gpx, options);
			if (!layers) return;
			_this.addLayer(layers);
			setTimeout(function(){ _this.fire('gpx_loaded') }, 0);
		}

		if (async == undefined) async = this.options.async;
		if (options == undefined) options = this.options;

		var gpx_content_to_parse = this._htmlspecialchars_decode(this.options.gpx_content);
		var xmlDoc = this._parse_xml(gpx_content_to_parse);
		if(xmlDoc){
			cb(this._parse_xml(gpx_content_to_parse), options);
		} else{
			if (window.console) { console.log('error parsing xml'); }
		}
	},

	_parse_xml: function(xmlStr){
		if (typeof window.DOMParser != "undefined") {
			return ( new window.DOMParser() ).parseFromString(xmlStr, "text/xml");
		} else if (typeof window.ActiveXObject != "undefined" && new window.ActiveXObject("Microsoft.XMLDOM")) {
			var xmlDoc = new window.ActiveXObject("Microsoft.XMLDOM");
			xmlDoc.async = "false";
			xmlDoc.loadXML(xmlStr);
			return xmlDoc;
		} else {
			throw new Error("No XML parser found");
		}
	},

	_parse_gpx_data: function(xml, options) {
		var j, i, el, layers = [];
		var tags = [['rte','rtept'], ['trkseg','trkpt']];
		var name = xml.getElementsByTagName('name');
		if (name.length > 0) {
			this._info.name = name[0].textContent || name[0].text || name[0].innerText;
		}
		for (j = 0; j < tags.length; j++) {
			el = xml.getElementsByTagName(tags[j][0]);
			for (i = 0; i < el.length; i++) {
					var coords = this._parse_trkseg(el[i], xml, options, tags[j][1]);
					if (coords.length === 0) continue;

					// add track
					var l = new L.Polyline(coords, options.polyline_options);
					this.fire('addline', { line: l })
					layers.push(l);

					if (mapsmarkerjspro.gpx_icons_status == 'show') { //info: added by RH
						// add start pin
						var p = new L.Marker(coords[0], {
						  interactive: false,
							icon: new L.GPXTrackIcon({iconUrl: options.marker_options.startIconUrl})
						});
						this.fire('addpoint', { point: p });
						layers.push(p);
						// add end pin
						p = new L.Marker(coords[coords.length-1], {
						  interactive: false,
						  icon: new L.GPXTrackIcon({iconUrl: options.marker_options.endIconUrl})
						});
						this.fire('addpoint', { point: p });
						layers.push(p);
					}
			}
		}

		this._info.hr.avg = Math.round(this._info.hr._total / this._info.hr._points.length);

		if (!layers.length) return;
		var layer = layers[0];
		if (layers.length > 1)
		  layer = new L.FeatureGroup(layers);
		return layer;
	},

	_parseDate_for_IE8: function(input) {
		var iso = /^(\d{4})(?:-?W(\d+)(?:-?(\d+)D?)?|(?:-(\d+))?-(\d+))(?:[T ](\d+):(\d+)(?::(\d+)(?:\.(\d+))?)?)?(?:Z(-?\d*))?$/;
		var parts = input.match(iso);
		if (parts == null) {
			throw new Error("Invalid Date");
		}
		var year = Number(parts[1]);
		if (typeof parts[2] != "undefined") {
			/* Convert weeks to days, months 0 */
			var weeks = Number(parts[2]) - 1;
			var days  = Number(parts[3]);
			if (typeof days == "undefined") {
				days = 0;
			}
			days += weeks * 7;
			var months = 0;
		} else {
			if (typeof parts[4] != "undefined") {
				var months = Number(parts[4]) - 1;
			} else {
				/* it's an ordinal date... */
				var months = 0;
			}
			var days   = Number(parts[5]);
		}

		if (typeof parts[6] != "undefined" &&
			typeof parts[7] != "undefined")
		{
			var hours        = Number(parts[6]);
			var minutes      = Number(parts[7]);

			if (typeof parts[8] != "undefined") {
				var seconds      = Number(parts[8]);

				if (typeof parts[9] != "undefined") {
					var fractional   = Number(parts[9]);
					var milliseconds = fractional / 100;
				} else {
					var milliseconds = 0
				}
			} else {
				var seconds      = 0;
				var milliseconds = 0;
			}
		}
		else {
			var hours        = 0;
			var minutes      = 0;
			var seconds      = 0;
			var fractional   = 0;
			var milliseconds = 0;
		}

		if (typeof parts[10] != "undefined") {
			/* Timezone adjustment, offset the minutes appropriately */
			var localzone = -(new Date().getTimezoneOffset());
			var timezone  = parts[10] * 60;
			minutes = Number(minutes) + (timezone - localzone);
		}
		return new Date(year, months, days, hours, minutes, seconds, milliseconds);
	},

	_parse_trkseg: function(line, xml, options, tag) {
		var el = line.getElementsByTagName(tag);
		if (!el.length) return [];
		var coords = [];
		var last = null;

		for (var i = 0; i < el.length; i++) {
			var _, ll = new L.LatLng(
				el[i].getAttribute('lat'),
				el[i].getAttribute('lon'));
			ll.meta = { time: null, ele: null, hr: null };

			_ = el[i].getElementsByTagName('time');
			if (_.length > 0) {
				if (window.addEventListener) {
					var time_temp = Date.parse(_[0].textContent);
					ll.meta.time = new Date(time_temp);
				} else { //IE8
					ll.meta.time = this._parseDate_for_IE8(_[0].text);
				}
			}

			_ = el[i].getElementsByTagName('ele');
			if (_.length > 0) {
				ll.meta.ele = parseFloat(_[0].textContent || _[0].text || _[0].innerText); //IE8
			}

			/*IE9+only _ = el[i].getElementsByTagNameNS('*', 'hr');*/
			_ = el[i].getElementsByTagName('hr');

			if (_.length > 0) {
				ll.meta.hr = parseInt(_[0].textContent || _[0].text || _[0].innerText); //IE8
				this._info.hr._points.push([this._info.length, ll.meta.hr]);
				this._info.hr._total += ll.meta.hr;
			}

			this._info.elevation._points.push([this._info.length, ll.meta.ele]);
			this._info.duration.end = ll.meta.time;

			if (last != null) {
				this._info.length += this._dist3d(last, ll);

				var t = ll.meta.ele - last.meta.ele;
				if (t > 0) this._info.elevation.gain += t;
				else this._info.elevation.loss += Math.abs(t);

				t = Math.abs(ll.meta.time - last.meta.time);
				this._info.duration.total += t;
				if (t < options.max_point_interval) this._info.duration.moving += t;
			} else {
				this._info.duration.start = ll.meta.time;
			}
			last = ll;
			coords.push(ll);
		}
		return coords;
	},

	_dist2d: function(a, b) {
		var R = 6371000;
		var dLat = this._deg2rad(b.lat - a.lat);
		var dLon = this._deg2rad(b.lng - a.lng);
		var r = Math.sin(dLat/2) *
				Math.sin(dLat/2) +
				Math.cos(this._deg2rad(a.lat)) *
				Math.cos(this._deg2rad(b.lat)) *
				Math.sin(dLon/2) *
				Math.sin(dLon/2);
		var c = 2 * Math.atan2(Math.sqrt(r), Math.sqrt(1-r));
		var d = R * c;
		return d;
	},

	_dist3d: function(a, b) {
		var planar = this._dist2d(a, b);
		var height = Math.abs(b.meta.ele - a.meta.ele);
		return Math.sqrt(Math.pow(planar, 2) + Math.pow(height, 2));
	},

	_deg2rad: function(deg) {
		return deg * Math.PI / 180;
	}
});

/*
Copyright (c) 2016 Dominik Moritz, v0.58/28.11.2016
This file is part of the leaflet locate control. It is licensed under the MIT license.
You can find the project at: https://github.com/domoritz/leaflet-locatecontrol
*/
(function (factory, window) {
     // see https://github.com/Leaflet/Leaflet/blob/master/PLUGIN-GUIDE.md#module-loaders
     // for details on how to structure a leaflet plugin.

    // define an AMD module that relies on 'leaflet'
    if (typeof define === 'function' && define.amd) {
        define(['leaflet'], factory);

    // define a Common JS module that relies on 'leaflet'
    } else if (typeof exports === 'object') {
        if (typeof window !== 'undefined' && window.L) {
            module.exports = factory(L);
        } else {
            module.exports = factory(require('leaflet'));
        }
    }

    // attach your plugin to the global 'L' variable
    if (typeof window !== 'undefined' && window.L){
        window.L.Control.Locate = factory(L);
    }
} (function (L) {
    var LocateControl = L.Control.extend({
        options: {
            /** Position of the control */
            position: 'topleft',
            /** The layer that the user's location should be drawn on. By default creates a new layer. */
            layer: undefined,
            /**
             * Automatically sets the map view (zoom and pan) to the user's location as it updates.
             * While the map is following the user's location, the control is in the `following` state,
             * which changes the style of the control and the circle marker.
             *
             * Possible values:
             *  - false: never updates the map view when location changes.
             *  - 'once': set the view when the location is first determined
             *  - 'always': always updates the map view when location changes.
             *              The map view follows the users location.
             *  - 'untilPan': (default) like 'always', except stops updating the
             *                view if the user has manually panned the map.
             *                The map view follows the users location until she pans.
             */
            setView: 'untilPan',
            /** Keep the current map zoom level when setting the view and only pan. */
            keepCurrentZoomLevel: false,
            /** Smooth pan and zoom to the location of the marker. Only works in Leaflet 1.0+. */
            flyTo: false,
            /**
             * The user location can be inside and outside the current view when the user clicks on the
             * control that is already active. Both cases can be configures separately.
             * Possible values are:
             *  - 'setView': zoom and pan to the current location
             *  - 'stop': stop locating and remove the location marker
             */
            clickBehavior: {
                /** What should happen if the user clicks on the control while the location is within the current view. */
                inView: 'stop',
                /** What should happen if the user clicks on the control while the location is outside the current view. */
                outOfView: 'setView',
            },
            /**
             * If set, save the map bounds just before centering to the user's
             * location. When control is disabled, set the view back to the
             * bounds that were saved.
             */
            returnToPrevBounds: false,
            /** If set, a circle that shows the location accuracy is drawn. */
            drawCircle: true,
            /** If set, the marker at the users' location is drawn. */
            drawMarker: true,
            /** The class to be used to create the marker. For example L.CircleMarker or L.Marker */
            markerClass: L.CircleMarker,
            /** Accuracy circle style properties. */
            circleStyle: {
                color: '#136AEC',
                fillColor: '#136AEC',
                fillOpacity: 0.15,
                weight: 2,
                opacity: 0.5
            },
            /** Inner marker style properties. Only works if your marker class supports `setStyle`. */
            markerStyle: {
                color: '#136AEC',
                fillColor: '#2A93EE',
                fillOpacity: 0.7,
                weight: 2,
                opacity: 0.9,
                radius: 5
            },
            /**
             * Changes to accuracy circle and inner marker while following.
             * It is only necessary to provide the properties that should change.
             */
            followCircleStyle: {},
            followMarkerStyle: {
                // color: '#FFA500',
                // fillColor: '#FFB000'
            },
            /** The CSS class for the icon. For example fa-location-arrow or fa-map-marker */
            icon: 'icon-location',  // fa-location-arrow or fa-map-marker //RH-changed
            iconLoading: 'icon-spinner animate-spin', //RH-changed
            /** The element to be created for icons. For example span or i */
            iconElementTag: 'span',
            /** Padding around the accuracy circle. */
            circlePadding: [0, 0],
            /** Use metric units. */
            metric: true,
            /** This event is called in case of any location error that is not a time out error. */
            onLocationError: function(err, control) {
                alert(err.message);
            },
            /**
             * This even is called when the user's location is outside the bounds set on the map.
             * The event is called repeatedly when the location changes.
             */
            onLocationOutsideMapBounds: function(control) {
                control.stop();
                alert(control.options.strings.outsideMapBoundsMsg);
            },
            /** Display a pop-up when the user click on the inner marker. */
            showPopup: true,
            strings: {
                title: "Show me where I am",
                metersUnit: "meters",
                feetUnit: "feet",
                popup: "You are within {distance} {unit} from this point",
                outsideMapBoundsMsg: "You seem located outside the boundaries of the map"
            },
            /** The default options passed to leaflets locate method. */
            locateOptions: {
                maxZoom: Infinity,
                watch: true,  // if you overwrite this, visualization cannot be updated
                setView: false // have to set this to false because we have to
                               // do setView manually
            }
        },

        initialize: function (options) {
            // set default options if nothing is set (merge one step deep)
            for (var i in options) {
                if (typeof this.options[i] === 'object') {
                    L.extend(this.options[i], options[i]);
                } else {
                    this.options[i] = options[i];
                }
            }

            // extend the follow marker style and circle from the normal style
            this.options.followMarkerStyle = L.extend({}, this.options.markerStyle, this.options.followMarkerStyle);
            this.options.followCircleStyle = L.extend({}, this.options.circleStyle, this.options.followCircleStyle);
        },

        /**
         * Add control to map. Returns the container for the control.
         */
        onAdd: function (map) {
            var container = L.DomUtil.create('div',
                'leaflet-control-locate leaflet-bar leaflet-control');

            this._layer = this.options.layer || new L.LayerGroup();
            this._layer.addTo(map);
            this._event = undefined;
            this._prevBounds = null;

            this._link = L.DomUtil.create('a', 'leaflet-bar-part leaflet-bar-part-single ' + this.options.icon, container); //RH-angepasst
            this._link.href = '#';
            this._link.title = this.options.strings.title;
            this._icon = L.DomUtil.create(this.options.iconElementTag, this.options.icon, this._link);

            L.DomEvent
                .on(this._link, 'click', L.DomEvent.stopPropagation)
                .on(this._link, 'click', L.DomEvent.preventDefault)
                .on(this._link, 'click', this._onClick, this)
                .on(this._link, 'dblclick', L.DomEvent.stopPropagation);

            this._resetVariables();

            this._map.on('unload', this._unload, this);

            return container;
        },

        /**
         * This method is called when the user clicks on the control.
         */
        _onClick: function() {
            this._justClicked = true;
            this._userPanned = false;

            if (this._active && !this._event) {
                // click while requesting
                this.stop();
            } else if (this._active && this._event !== undefined) {
                var behavior = this._map.getBounds().contains(this._event.latlng) ?
                    this.options.clickBehavior.inView : this.options.clickBehavior.outOfView;
                switch (behavior) {
                    case 'setView':
                        this.setView();
                        break;
                    case 'stop':
                        this.stop();
                        if (this.options.returnToPrevBounds) {
                            var f = this.options.flyTo ? this._map.flyToBounds : this._map.fitBounds;
                            f.bind(this._map)(this._prevBounds);
                        }
                        break;
                }
            } else {
                if (this.options.returnToPrevBounds) {
                  this._prevBounds = this._map.getBounds();
                }
                this.start();
            }

            this._updateContainerStyle();
        },

        /**
         * Starts the plugin:
         * - activates the engine
         * - draws the marker (if coordinates available)
         */
        start: function() {
            this._activate();

            if (this._event) {
                this._drawMarker(this._map);

                // if we already have a location but the user clicked on the control
                if (this.options.setView) {
                    this.setView();
                }
            }
            this._updateContainerStyle();
        },

        /**
         * Stops the plugin:
         * - deactivates the engine
         * - reinitializes the button
         * - removes the marker
         */
        stop: function() {
            this._deactivate();

            this._cleanClasses();
            this._resetVariables();

            this._removeMarker();
        },

        /**
         * This method launches the location engine.
         * It is called before the marker is updated,
         * event if it does not mean that the event will be ready.
         *
         * Override it if you want to add more functionalities.
         * It should set the this._active to true and do nothing if
         * this._active is true.
         */
        _activate: function() {
            if (!this._active) {
                this._map.locate(this.options.locateOptions);
                this._active = true;

                // bind event listeners
                this._map.on('locationfound', this._onLocationFound, this);
                this._map.on('locationerror', this._onLocationError, this);
                this._map.on('dragstart', this._onDrag, this);
            }
        },

        /**
         * Called to stop the location engine.
         *
         * Override it to shutdown any functionalities you added on start.
         */
        _deactivate: function() {
            this._map.stopLocate();
            this._active = false;

            // unbind event listeners
            this._map.off('locationfound', this._onLocationFound, this);
            this._map.off('locationerror', this._onLocationError, this);
            this._map.off('dragstart', this._onDrag, this);
        },

        /**
         * Zoom (unless we should keep the zoom level) and an to the current view.
         */
        setView: function() {
            this._drawMarker();
            if (this._isOutsideMapBounds()) {
                this.options.onLocationOutsideMapBounds(this);
            } else {
                if (this.options.keepCurrentZoomLevel) {
                    var f = this.options.flyTo ? this._map.flyTo : this._map.panTo;
                    f.bind(this._map)([this._event.latitude, this._event.longitude]);
                } else {
                    var f = this.options.flyTo ? this._map.flyToBounds : this._map.fitBounds;
                    f.bind(this._map)(this._event.bounds, {
                        padding: this.options.circlePadding,
                        maxZoom: this.options.locateOptions.maxZoom
                    });
                }
            }
        },

        /**
         * Draw the marker and accuracy circle on the map.
         *
         * Uses the event retrieved from onLocationFound from the map.
         */
        _drawMarker: function() {
            if (this._event.accuracy === undefined) {
                this._event.accuracy = 0;
            }

            var radius = this._event.accuracy;
            var latlng = this._event.latlng;

            // circle with the radius of the location's accuracy
            if (this.options.drawCircle) {
                var style = this._isFollowing() ? this.options.followCircleStyle : this.options.circleStyle;

                if (!this._circle) {
                    this._circle = L.circle(latlng, radius, style).addTo(this._layer);
                } else {
                    this._circle.setLatLng(latlng).setRadius(radius).setStyle(style);
                }
            }

            var distance, unit;
            if (this.options.metric) {
                distance = radius.toFixed(0);
                unit =  this.options.strings.metersUnit;
            } else {
                distance = (radius * 3.2808399).toFixed(0);
                unit = this.options.strings.feetUnit;
            }

            // small inner marker
            if (this.options.drawMarker) {
                var mStyle = this._isFollowing() ? this.options.followMarkerStyle : this.options.markerStyle;
                if (!this._marker) {
                    this._marker = new this.options.markerClass(latlng, mStyle).addTo(this._layer);
                } else {
                    this._marker.setLatLng(latlng);
                    // If the markerClass can be updated with setStyle, update it.
                    if (this._marker.setStyle) {
                        this._marker.setStyle(mStyle);
                    }
                }
            }

            var t = this.options.strings.popup;
            if (this.options.showPopup && t && this._marker) {
                this._marker
                    .bindPopup(L.Util.template(t, {distance: distance, unit: unit}))
                    ._popup.setLatLng(latlng);
            }
        },

        /**
         * Remove the marker from map.
         */
        _removeMarker: function() {
            this._layer.clearLayers();
            this._marker = undefined;
            this._circle = undefined;
        },

        /**
         * Unload the plugin and all event listeners.
         * Kind of the opposite of onAdd.
         */
        _unload: function() {
            this.stop();
            this._map.off('unload', this._unload, this);
        },

        /**
         * Calls deactivate and dispatches an error.
         */
        _onLocationError: function(err) {
            // ignore time out error if the location is watched
            if (err.code == 3 && this.options.locateOptions.watch) {
                return;
            }

            this.stop();
            this.options.onLocationError(err, this);
        },

        /**
         * Stores the received event and updates the marker.
         */
        _onLocationFound: function(e) {
            // no need to do anything if the location has not changed
            if (this._event &&
                (this._event.latlng.lat === e.latlng.lat &&
                 this._event.latlng.lng === e.latlng.lng &&
                     this._event.accuracy === e.accuracy)) {
                return;
            }

            if (!this._active) {
                // we may have a stray event
                return;
            }

            this._event = e;

            this._drawMarker();
            this._updateContainerStyle();

            switch (this.options.setView) {
                case 'once':
                    if (this._justClicked) {
                        this.setView();
                    }
                    break;
                case 'untilPan':
                    if (!this._userPanned) {
                        this.setView();
                    }
                    break;
                case 'always':
                    this.setView();
                    break;
                case false:
                    // don't set the view
                    break;
            }

            this._justClicked = false;
        },

        /**
         * When the user drags. Need a separate even so we can bind and unbind even listeners.
         */
        _onDrag: function() {
            // only react to drags once we have a location
            if (this._event) {
                this._userPanned = true;
                this._updateContainerStyle();
                this._drawMarker();
            }
        },

        /**
         * Compute whether the map is following the user location with pan and zoom.
         */
        _isFollowing: function() {
            if (!this._active) {
                return false;
            }

            if (this.options.setView === 'always') {
                return true;
            } else if (this.options.setView === 'untilPan') {
                return !this._userPanned;
            }
        },

        /**
         * Check if location is in map bounds
         */
        _isOutsideMapBounds: function() {
            if (this._event === undefined) {
                return false;
            }
            return this._map.options.maxBounds &&
                !this._map.options.maxBounds.contains(this._event.latlng);
        },

        /**
         * Toggles button class between following and active.
         */
        _updateContainerStyle: function() {
            if (!this._container) {
                return;
            }

            if (this._active && !this._event) {
                // active but don't have a location yet
                this._setClasses('requesting');
            } else if (this._isFollowing()) {
                this._setClasses('following');
            } else if (this._active) {
                this._setClasses('active');
            } else {
                this._cleanClasses();
            }
        },

        /**
         * Sets the CSS classes for the state.
         */
        _setClasses: function(state) {
            if (state == 'requesting') {
                L.DomUtil.removeClasses(this._container, "active following");
                L.DomUtil.addClasses(this._container, "requesting");

                L.DomUtil.removeClasses(this._icon, this.options.icon);
                L.DomUtil.addClasses(this._icon, this.options.iconLoading);
            } else if (state == 'active') {
                L.DomUtil.removeClasses(this._container, "requesting following");
                L.DomUtil.addClasses(this._container, "active");

                L.DomUtil.removeClasses(this._icon, this.options.iconLoading);
                L.DomUtil.addClasses(this._icon, this.options.icon);
            } else if (state == 'following') {
                L.DomUtil.removeClasses(this._container, "requesting");
                L.DomUtil.addClasses(this._container, "active following");

                L.DomUtil.removeClasses(this._icon, this.options.iconLoading);
                L.DomUtil.addClasses(this._icon, this.options.icon);
            }
        },

        /**
         * Removes all classes from button.
         */
        _cleanClasses: function() {
            L.DomUtil.removeClass(this._container, "requesting");
            L.DomUtil.removeClass(this._container, "active");
            L.DomUtil.removeClass(this._container, "following");

            L.DomUtil.removeClasses(this._icon, this.options.iconLoading);
            L.DomUtil.addClasses(this._icon, this.options.icon);
        },

        /**
         * Reinitializes state variables.
         */
        _resetVariables: function() {
            // whether locate is active or not
            this._active = false;

            // true if the control was clicked for the first time
            // we need this so we can pan and zoom once we have the location
            this._justClicked = false;

            // true if the user has panned the map after clicking the control
            this._userPanned = false;
        }
    });

    L.control.locate = function (options) {
        return new L.Control.Locate(options);
    };

    (function(){
      // leaflet.js raises bug when trying to addClass / removeClass multiple classes at once
      // Let's create a wrapper on it which fixes it.
      var LDomUtilApplyClassesMethod = function(method, element, classNames) {
        classNames = classNames.split(' ');
        classNames.forEach(function(className) {
            L.DomUtil[method].call(this, element, className);
        });
      };

      L.DomUtil.addClasses = function(el, names) { LDomUtilApplyClassesMethod('addClass', el, names); };
      L.DomUtil.removeClasses = function(el, names) { LDomUtilApplyClassesMethod('removeClass', el, names); };
    })();

    return LocateControl;
}, window));

/*
URL hashes - based on leaflet-hash.js, Copyright (c) 2013 @mlevans, MIT License
https://github.com/mlevans/leaflet-hash
*/
(function(window) {
	var HAS_HASHCHANGE = (function() {
		var doc_mode = window.documentMode;
		return ('onhashchange' in window) &&
			(doc_mode === undefined || doc_mode > 7);
	})();

	L.Hash = function(map) {
		if(typeof window['leaflet_hash_requested'] == 'undefined'){

				this.onHashChange = L.Util.bind(this.onHashChange, this);

				if (map) {
					this.init(map);
				}
				this.events = new Object();
				window['leaflet_hash_requested'] = true;

		}
	};

	L.Hash.parseHash = function(hash) {
		if(hash.indexOf('#') === 0) {
			hash = hash.substr(1);
		}
		var args = hash.split("/");
		if (args.length == 3) {
			var zoom = parseInt(args[0], 10),
			lat = parseFloat(args[1]),
			lon = parseFloat(args[2]);
			if (isNaN(zoom) || isNaN(lat) || isNaN(lon)) {
				return false;
			} else {
				return {
					center: new L.LatLng(lat, lon),
					zoom: zoom
				};
			}
		} else {
			return false;
		}
	};

	L.Hash.formatHash = function(map) {
		var center = map.getCenter(),
		    zoom = map.getZoom(),
		    precision = Math.max(0, Math.ceil(Math.log(zoom) / Math.LN2));

			return "#" + [zoom,
				center.lat.toFixed(precision),
				center.lng.toFixed(precision)
			].join("/");
	},

	L.Hash.prototype = {
		map: null,
		lastHash: null,
		parseHash: L.Hash.parseHash,
		formatHash: L.Hash.formatHash,

		init: function(map) {
			this.map = map;

			// reset the hash
			this.lastHash = null;
			this.onHashChange();
			this.startListening();
		},

		removeFrom: function(map) {
			if (this.changeTimeout) {
				clearTimeout(this.changeTimeout);
			}

			this.stopListening();

			this.map = null;
		},

		onMapMove: function() {
			// bail if we're moving the map (updating from a hash),
			// or if the map is not yet loaded
			if (this.movingMap || !this.map._loaded) {
				return false;
			}
			var args = location.hash.split("/");
			//info: additional check to fix the conflict of possible hashes such as #top #bottom
			var second_char = location.hash.charAt(1);
			if (args.length != 3 && (!isNaN(parseFloat(second_char)) && isFinite(second_char))) {
				return false;
			}
			var hash = this.formatHash(this.map);
			if (this.events['change']) {
				for (var i=0; i<this.events['change'].length; i++) {
					hash = this.events['change'][i](hash);
				}
			}
			if (this.lastHash != hash) {
				location.replace(hash);
				this.lastHash = hash;
				if (this.events['hash']) {
					for (var i=0; i<this.events['hash'].length; i++) {
						this.events['hash'][i](hash);
					}
				}
			}


		},

		movingMap: false,
		update: function() {
			var hash = location.hash;
			if (hash === this.lastHash) {
				return;
			}
			var parsed = this.parseHash(hash);

			if (parsed) {
				this.movingMap = true;

				this.map.setView(parsed.center, parsed.zoom, { animate: false}); //info: PR https://github.com/mlevans/leaflet-hash/pull/37
				if (this.events['update']) {
					for (var i=0; i<this.events['update'].length; i++) {
						this.events['update'][i](hash);
					}
				}
				this.movingMap = false;
			} else {
				this.onMapMove(this.map);
			}
		},
		on: function(event, func) {
			if (! this.events[event]) {
				this.events[event] = [ func ];
			} else {
				this.events[event].push(func);
			}
		},
		off: function(event, func) {
			if (this.events[event]) {
				for (var i=0; i<this.events[event].length; i++) {
					if (this.events[event][i] == func) {
						this.events[event].splice(i);
						return;
					}
				}
			}
		},
		trigger: function(event) {
			if (event == "move") {
				if (! this.movingMap) {
					this.onMapMove();
				}
			}
		},
		// setMovingMap()/clearMovingMap() when making multiple changes that affect hash arguments
		//   ie when moving location and changing visible layers
		setMovingMap: function() {
			this.movingMap = true;
		},
		clearMovingMap: function() {
			this.movingMap = false;
		},
		// defer hash change updates every 100ms
		changeDefer: 100,
		changeTimeout: null,
		onHashChange: function() {
			// throttle calls to update() so that they only happen every
			// `changeDefer` ms
			if (!this.changeTimeout) {
				var that = this;
				this.changeTimeout = setTimeout(function() {
					that.update();
					that.changeTimeout = null;
				}, this.changeDefer);
			}
		},
		isListening: false,
		hashChangeInterval: null,
		startListening: function() {
			if (this.isListening) { return; }
			this.map.on("moveend", this.onMapMove, this);
			if (HAS_HASHCHANGE) {
				L.DomEvent.addListener(window, "hashchange", this.onHashChange);
			} else {
				clearInterval(this.hashChangeInterval);
				this.hashChangeInterval = setInterval(this.onHashChange, 50);
			}
			this.isListening = true;
		},

		stopListening: function() {
			if (!this.isListening) { return; }
			this.map.off("moveend", this.onMapMove, this);
			if (HAS_HASHCHANGE) {
				L.DomEvent.removeListener(window, "hashchange", this.onHashChange);
			} else {
				clearInterval(this.hashChangeInterval);
			}
			this.isListening = false;
		}
	};
	L.hash = function(map) {
		return new L.Hash(map);
	};
	L.Map.include({
		addHash: function(){
			this._hash = L.hash(this);
			return this;
		},

		removeHash: function(){
			this._hash.remove();
			return this;
		}
	});
})(window);

/*
 * Leaflet.MarkerCluster.LayerSupport sub-plugin for Leaflet.markercluster plugin, MIT license (expat type)
   v1.0.3 for Leaflet 1.0 (https://github.com/ghybs/Leaflet.MarkerCluster.LayerSupport)
 * Copyright (c) 2015 Boris Seang
*/
(function (root, factory) {
    if (typeof define === "function" && define.amd) {
        define(["leaflet"], factory);
    } else if (typeof module === "object" && module.exports) {
        factory(require("leaflet"));
    } else {
        factory(root.L);
    }
}(this, function (L, undefined) {

/*
* Code already from MarkerClusterGroupLayerSupport!
info: comment not in original repo anymore - added by Waseem/Thorsten?
*/
var LMCG = L.MarkerClusterGroup,
	LMCGproto = LMCG.prototype;

/**
 * Extends the L.MarkerClusterGroup class by mainly overriding methods for
 * addition/removal of layers, so that they can also be directly added/removed
 * from the map later on while still clustering in this group.
 * @type {L.MarkerClusterGroup}
 */
var MarkerClusterGroupLayerSupport = LMCG.extend({

	options: {
		// Buffer single addLayer and removeLayer requests for efficiency.
		singleAddRemoveBufferDuration: 100 // in ms.
	},

	initialize: function (options) {
		LMCGproto.initialize.call(this, options);

		// Replace the MCG internal featureGroup's so that they directly
		// access the map add/removal methods, bypassing the switch agent.
		this._featureGroup = new _ByPassingFeatureGroup();
		this._featureGroup.addEventParent(this);

		this._nonPointGroup = new _ByPassingFeatureGroup();
		this._nonPointGroup.addEventParent(this);

		// Keep track of what should be "represented" on map (can be clustered).
		this._layers = {};
		this._proxyLayerGroups = {};
		this._proxyLayerGroupsNeedRemoving = {};

		// Buffer single addLayer and removeLayer requests.
		this._singleAddRemoveBuffer = [];
	},

	/**
	 * Stamps the passed layers as being part of this group, but without adding
	 * them to the map right now.
	 * @param layers L.Layer|Array(L.Layer) layer(s) to be stamped.
	 * @returns {MarkerClusterGroupLayerSupport} this.
	 */
	checkIn: function (layers) {
		var layersArray = this._toArray(layers);

		this._checkInGetSeparated(layersArray);

		return this;
	},

	/**
	 * Un-stamps the passed layers from being part of this group. It has to
	 * remove them from map (if they are) since they will no longer cluster.
	 * @param layers L.Layer|Array(L.Layer) layer(s) to be un-stamped.
	 * @returns {MarkerClusterGroupLayerSupport} this.
	 */
	checkOut: function (layers) {
		var layersArray = this._toArray(layers),
			separated = this._separateSingleFromGroupLayers(layersArray, {
				groups: [],
				singles: []
			}),
			groups = separated.groups,
			singles = separated.singles,
			i, layer;

		// Un-stamp single layers.
		for (i = 0; i < singles.length; i++) {
			layer = singles[i];
			delete this._layers[L.stamp(layer)];
			delete layer._mcgLayerSupportGroup;
		}

		// Batch remove single layers from MCG.
		// Note: as for standard MCG, if single layers have been added to
		// another MCG in the meantime, their __parent will have changed,
		// so weird things would happen.
		this._originalRemoveLayers(singles);

		// Dismiss Layer Groups.
		for (i = 0; i < groups.length; i++) {
			layer = groups[i];
			this._dismissProxyLayerGroup(layer);
		}

		return this;
	},

	/**
	 * Checks in and adds an array of layers to this group.
	 * Layer Groups are also added to the map to fire their event.
	 * @param layers (L.Layer|L.Layer[]) single and/or group layers to be added.
	 * @returns {MarkerClusterGroupLayerSupport} this.
	 */
	addLayers: function (layers) {
		var layersArray = this._toArray(layers),
			separated = this._checkInGetSeparated(layersArray),
			groups = separated.groups,
			i, group, id;

		// Batch add all single layers.
		this._originalAddLayers(separated.singles);

		// Add Layer Groups to the map so that they are registered there and
		// the map fires 'layeradd' events for them as well.
		for (i = 0; i < groups.length; i++) {
			group = groups[i];
			id = L.stamp(group);
			this._proxyLayerGroups[id] = group;
			delete this._proxyLayerGroupsNeedRemoving[id];
			if (this._map) {
				this._map._originalAddLayer(group);
			}
		}
	},
	addLayer: function (layer) {
		this._bufferSingleAddRemove(layer, "addLayers");
		return this;
	},
	_originalAddLayer: LMCGproto.addLayer,
	_originalAddLayers: LMCGproto.addLayers,

	/**
	 * Removes layers from this group but without check out.
	 * Layer Groups are also removed from the map to fire their event.
	 * @param layers (L.Layer|L.Layer[]) single and/or group layers to be removed.
	 * @returns {MarkerClusterGroupLayerSupport} this.
	 */
	removeLayers: function (layers) {
		var layersArray = this._toArray(layers),
			separated = this._separateSingleFromGroupLayers(layersArray, {
				groups: [],
				singles: []
			}),
			groups = separated.groups,
			singles = separated.singles,
			i = 0,
			group, id;

		// Batch remove single layers from MCG.
		this._originalRemoveLayers(singles);

		// Remove Layer Groups from the map so that they are un-registered
		// there and the map fires 'layerremove' events for them as well.
		for (; i < groups.length; i++) {
			group = groups[i];
			id = L.stamp(group);
			delete this._proxyLayerGroups[id];
			if (this._map) {
				this._map._originalRemoveLayer(group);
			} else {
				this._proxyLayerGroupsNeedRemoving[id] = group;
			}
		}

		return this;
	},
	removeLayer: function (layer) {
		this._bufferSingleAddRemove(layer, "removeLayers");
		return this;
	},
	_originalRemoveLayer: LMCGproto.removeLayer,
	_originalRemoveLayers: LMCGproto.removeLayers,

	onAdd: function (map) {
		// Replace the map addLayer and removeLayer methods to place the
		// switch agent that redirects layers when required.
		map._originalAddLayer = map._originalAddLayer || map.addLayer;
		map._originalRemoveLayer = map._originalRemoveLayer || map.removeLayer;
		L.extend(map, _layerSwitchMap);

		// As this plugin allows the Application to add layers on map, some
		// checked in layers might have been added already, whereas LayerSupport
		// did not have a chance to inject the switch agent in to the map
		// (if it was never added to map before). Therefore we need to
		// remove all checked in layers from map!
		var toBeReAdded = this._removePreAddedLayers(map),
			id, group, i;

		// Normal MCG onAdd.
		LMCGproto.onAdd.call(this, map);

		// If layer Groups are added/removed from this group while it is not
		// on map, Control.Layers gets out of sync until this is added back.

		// Restore proxy Layer Groups that may have been added to this
		// group while it was off map.
		for (id in this._proxyLayerGroups) {
			group = this._proxyLayerGroups[id];
			map._originalAddLayer(group);
		}

		// Remove proxy Layer Groups that may have been removed from this
		// group while it was off map.
		for (id in this._proxyLayerGroupsNeedRemoving) {
			group = this._proxyLayerGroupsNeedRemoving[id];
			map._originalRemoveLayer(group);
			delete this._proxyLayerGroupsNeedRemoving[id];
		}

		// Restore Layers.
		for (i = 0; i < toBeReAdded.length; i++) {
			map.addLayer(toBeReAdded[i]);
		}
	},

	// Do not restore the original map methods when removing the group from it.
	// Leaving them as-is does not harm, whereas restoring the original ones
	// may kill the functionality of potential other LayerSupport groups on
	// the same map. Therefore we do not need to override onRemove.

	_bufferSingleAddRemove: function (layer, operationType) {
		var duration = this.options.singleAddRemoveBufferDuration,
			fn;

		if (duration > 0) {
			this._singleAddRemoveBuffer.push({
				type: operationType,
				layer: layer
			});

			if (!this._singleAddRemoveBufferTimeout) {
				fn = L.bind(this._processSingleAddRemoveBuffer, this);

				this._singleAddRemoveBufferTimeout = setTimeout(fn, duration);
			}
		} else { // If duration <= 0, process synchronously.
			this[operationType](layer);
		}
	},
	_processSingleAddRemoveBuffer: function () {
		// For now, simply cut the processes at each operation change
		// (addLayers, removeLayers).
		var singleAddRemoveBuffer = this._singleAddRemoveBuffer,
			i = 0,
			layersBuffer = [],
			currentOperation,
			currentOperationType;

		for (; i < singleAddRemoveBuffer.length; i++) {
			currentOperation = singleAddRemoveBuffer[i];
			if (!currentOperationType) {
				currentOperationType = currentOperation.type;
			}
			if (currentOperation.type === currentOperationType) {
				layersBuffer.push(currentOperation.layer);
			} else {
				this[currentOperationType](layersBuffer);
				layersBuffer = [currentOperation.layer];
			}
		}
		this[currentOperationType](layersBuffer);
		singleAddRemoveBuffer.length = 0;
		clearTimeout(this._singleAddRemoveBufferTimeout);
		this._singleAddRemoveBufferTimeout = null;
	},

	_checkInGetSeparated: function (layersArray) {
		var separated = this._separateSingleFromGroupLayers(layersArray, {
				groups: [],
				singles: []
			}),
			groups = separated.groups,
			singles = separated.singles,
			i, layer;

		// Recruit Layer Groups.
		// If they do not already belong to this group, they will be
		// removed from map (together will all child layers).
		for (i = 0; i < groups.length; i++) {
			layer = groups[i];
			this._recruitLayerGroupAsProxy(layer);
		}

		// Stamp single layers.
		for (i = 0; i < singles.length; i++) {
			layer = singles[i];

			// Remove from previous group first.
			this._removeFromOtherGroupsOrMap(layer);

			this._layers[L.stamp(layer)] = layer;
			layer._mcgLayerSupportGroup = this;
		}

		return separated;
	},

	_separateSingleFromGroupLayers: function (inputLayers, output) {
		var groups = output.groups,
			singles = output.singles,
			isArray = L.Util.isArray,
			layer;

		for (var i = 0; i < inputLayers.length; i++) {
			layer = inputLayers[i];

			if (layer instanceof L.LayerGroup) {
				groups.push(layer);
				this._separateSingleFromGroupLayers(layer.getLayers(), output);
				continue;
			} else if (isArray(layer)) {
				this._separateSingleFromGroupLayers(layer, output);
				continue;
			}

			singles.push(layer);
		}

		return output;
	},

	// Recruit the LayerGroup as a proxy, so that any layer that is added
	// to / removed from that group later on is also added to / removed from
	// this group.
	// Check in and addition of already contained markers must be taken care
	// of externally.
	_recruitLayerGroupAsProxy: function (layerGroup) {
		var otherMcgLayerSupportGroup = layerGroup._proxyMcgLayerSupportGroup;

		// If it is not yet in this group, remove it from previous group
		// or from map.
		if (otherMcgLayerSupportGroup) {
			if (otherMcgLayerSupportGroup === this) {
				return;
			}
			// Remove from previous Layer Support group first.
			// It will also be removed from map with child layers.
			otherMcgLayerSupportGroup.checkOut(layerGroup);
		} else {
			this._removeFromOwnMap(layerGroup);
		}

		layerGroup._proxyMcgLayerSupportGroup = this;
		layerGroup._originalAddLayer =
			layerGroup._originalAddLayer || layerGroup.addLayer;
		layerGroup._originalRemoveLayer =
			layerGroup._originalRemoveLayer || layerGroup.removeLayer;
		L.extend(layerGroup, _proxyLayerGroup);
	},

	// Restore the normal LayerGroup behaviour.
	// Removal and check out of contained markers must be taken care of externally.
	_dismissProxyLayerGroup: function (layerGroup) {
		if (layerGroup._proxyMcgLayerSupportGroup === undefined ||
			layerGroup._proxyMcgLayerSupportGroup !== this) {

			return;
		}

		delete layerGroup._proxyMcgLayerSupportGroup;
		layerGroup.addLayer = layerGroup._originalAddLayer;
		layerGroup.removeLayer = layerGroup._originalRemoveLayer;

		var id = L.stamp(layerGroup);
		delete this._proxyLayerGroups[id];
		delete this._proxyLayerGroupsNeedRemoving[id];

		this._removeFromOwnMap(layerGroup);
	},

	_removeFromOtherGroupsOrMap: function (layer) {
		var otherMcgLayerSupportGroup = layer._mcgLayerSupportGroup;

		if (otherMcgLayerSupportGroup) { // It is a Layer Support group.
			if (otherMcgLayerSupportGroup === this) {
				return;
			}
			otherMcgLayerSupportGroup.checkOut(layer);

		} else if (layer.__parent) { // It is in a normal MCG.
			layer.__parent._group.removeLayer(layer);

		} else { // It could still be on a map.
			this._removeFromOwnMap(layer);
		}
	},

	// Remove layers that are being checked in, because they can now cluster.
	_removeFromOwnMap: function (layer) {
		if (layer._map) {
			// This correctly fires layerremove event for Layer Groups as well.
			layer._map.removeLayer(layer);
		}
	},

	// In case checked in layers have been added to map whereas map is not redirected.
	_removePreAddedLayers: function (map) {
		var layers = this._layers,
			toBeReAdded = [],
			layer;

		for (var id in layers) {
			layer = layers[id];
			if (layer._map) {
				toBeReAdded.push(layer);
				map._originalRemoveLayer(layer);
			}
		}

		return toBeReAdded;
	},

	_toArray: function (item) {
		return L.Util.isArray(item) ? item : [item];
	}

});

/**
 * Extends the FeatureGroup by overriding add/removal methods that directly
 * access the map original methods, bypassing the switch agent.
 * Used internally in Layer Support for _featureGroup and _nonPointGroup only.
 * @type {L.FeatureGroup}
 * @private
 */
var _ByPassingFeatureGroup = L.FeatureGroup.extend({

	// Re-implement just to change the map method.
	addLayer: function (layer) {
		if (this.hasLayer(layer)) {
			return this;
		}

		layer.addEventParent(this);

		var id = L.stamp(layer);

		this._layers[id] = layer;

		if (this._map) {
			// Use the original map addLayer.
			this._map._originalAddLayer(layer);
		}

		return this.fire('layeradd', {layer: layer});
	},

	// Re-implement just to change the map method.
	removeLayer: function (layer) {
		if (!this.hasLayer(layer)) {
			return this;
		}
		if (layer in this._layers) {
			layer = this._layers[layer];
		}

		layer.removeEventParent(this);

		var id = L.stamp(layer);

		if (this._map && this._layers[id]) {
			// Use the original map removeLayer.
			this._map._originalRemoveLayer(this._layers[id]);
		}

		delete this._layers[id];

		return this.fire('layerremove', {layer: layer});
	},

	onAdd: function (map) {
		this._map = map;
		// Use the original map addLayer.
		this.eachLayer(map._originalAddLayer, map);
	},

	onRemove: function (map) {
		// Use the original map removeLayer.
		this.eachLayer(map._originalRemoveLayer, map);
		this._map = null;
	}

});

/**
 * Toolbox to equip LayerGroups recruited as proxy.
 * @type {{addLayer: Function, removeLayer: Function}}
 * @private
 */
var _proxyLayerGroup = {

	// Re-implement to redirect addLayer to Layer Support group instead of map.
	addLayer: function (layer) {
		var id = this.getLayerId(layer);

		this._layers[id] = layer;

		if (this._map) {
			this._proxyMcgLayerSupportGroup.addLayer(layer);
		} else {
			this._proxyMcgLayerSupportGroup.checkIn(layer);
		}

		return this;
	},

	// Re-implement to redirect removeLayer to Layer Support group instead of map.
	removeLayer: function (layer) {

		var id = layer in this._layers ? layer : this.getLayerId(layer);

		this._proxyMcgLayerSupportGroup.removeLayer(layer);

		delete this._layers[id];

		return this;
	}

};

/**
 * Toolbox to equip the Map with a switch agent that redirects layers
 * addition/removal to their Layer Support group when defined.
 * @type {{addLayer: Function, removeLayer: Function}}
 * @private
 */
var _layerSwitchMap = {

	addLayer: function (layer) {
		if (layer._mcgLayerSupportGroup) {
			// Use the original MCG addLayer.
			return layer._mcgLayerSupportGroup._originalAddLayer(layer);
		}

		return this._originalAddLayer(layer);
	},

	removeLayer: function (layer) {
		if (layer._mcgLayerSupportGroup) {
			// Use the original MCG removeLayer.
			return layer._mcgLayerSupportGroup._originalRemoveLayer(layer);
		}

		return this._originalRemoveLayer(layer);
	}

};

// Supply with a factory for consistency with Leaflet.
L.markerClusterGroup.layerSupport = function (options) {
	return new MarkerClusterGroupLayerSupport(options);
};


}));
//# sourceMappingURL=leaflet.markercluster.layersupport-src.map

/*
Leaflet zoom control with a home button for resetting the view.
By torfsen, based code by toms (https://gis.stackexchange.com/a/127383/48264), license: Creative Commons Attribution ShareAlike (CC BY-SA 3.0)
Last commit: 2.8.2016, "use HTTPS URLs in demo (https://github.com/torfsen/leaflet.zoomhome/commits/master)
Additional changes: remove "reset: true" & change "animate: true" #383
*/
(function () {
    "use strict";

    L.Control.ZoomHome = L.Control.extend({
        options: {
            position: 'topleft',
            mapId: '',
            mapnameJS: '',
            ondemand: false,
            zoomHomeTitle: 'Home',
            homeCoordinates: null,
            homeZoom: null,
            reenableClustering: false
        },

        onAdd: function (map) {
            var controlName = 'leaflet-control-zoomhome',
                container = L.DomUtil.create('div', controlName + ' leaflet-bar'),
                options = this.options;

            container.setAttribute('id', 'leaflet-control-zoomhome-' + options.mapId);
            if(options.ondemand == true){
            	container.style.display = 'none';
            }
            if (options.homeCoordinates === null) {
                options.homeCoordinates = map.getCenter();
            }
            if (options.homeZoom === null) {
                options.homeZoom = map.getZoom();
            }

            var zoomHomeText = '<span class="lmm-icon-zoomhome"></span>';
            this._zoomHomeButton = this._createButton(zoomHomeText, options.zoomHomeTitle,
                controlName + '-home lmm-icon-zoomhome', container, this._zoomHome.bind(this));
            return container;
        },

        _zoomHome: function (e) {
            //jshint unused:false
			var reenableClustering = this.options.reenableClustering;
            this._map.closePopup();
            this._map.setView(this.options.homeCoordinates, this.options.homeZoom, {animate:true});
			if (reenableClustering === 'true') {
				window['markercluster_' + this.options.mapnameJS].enableClustering();
			}
            var mapId = this.options.mapId;
            var ondemand = this.options.ondemand;
            setTimeout(function(){
            	if(ondemand == true){
             		document.getElementById('leaflet-control-zoomhome-' + mapId).style.display = 'none';
            	}
            },500);
        },
        _createButton: function (html, title, className, container, fn) {
			var link = L.DomUtil.create('a', className, container);
			link.innerHTML = html;
			link.href = '#';
			link.title = title;

			L.DomEvent
			    .on(link, 'mousedown dblclick', L.DomEvent.stopPropagation)
			    .on(link, 'click', L.DomEvent.stop)
			    .on(link, 'click', fn, this)
			    .on(link, 'click', this._refocusOnMap, this);

			return link;
		}
    });

    L.Control.zoomHome = function (options) {
        return new L.Control.ZoomHome(options);
    };
}());
/*
Javascript Events API for LeafletJS, https://mapsmarker.com/jseventsapi/
*/
var MMP = {
	maps:{
		byId: function(map_id){
				return window[MMP.maps[map_id]];
		},
		onAll: function(event, handler){
			jQuery.each(MMP.maps,function(key, map){
				if(!isNaN(key)){
					window[map].on(event, handler);
				}
			});
		},
		toAll: function(callback){
			jQuery.each(MMP.maps,function(key, map){
				if(!isNaN(key)){
					callback(window[map]);
				}
			});
		},
		getAllMarkers: function(){
			var all_markers = [];
			jQuery.each(MMP.maps,function(key, map){
				if(typeof window[MMP.maps[key]] != "undefined"){
					jQuery.each(window[MMP.maps[key]]._layers, function (i, layer) {
						if(typeof layer.feature != 'undefined'){
							all_markers[ layer.feature.properties.markerid ] = this;
						}
					});
				}
			});
			return all_markers;
		},
		getMarker: function(layer_id, marker_id){
			var marker;
			jQuery.each(window[MMP.maps[layer_id]]._layers, function (i, layer) {
				if(typeof layer.feature != 'undefined'){
					if(layer.feature.properties.markerid == marker_id){
						marker = this;
					}
				}
			});
			return marker;
		}
	}
};
/*
 Maps filters
*/
L.Control.Filters = L.Control.Layers.extend({
	options: {
		position: 'topright',
		autoZIndex: true,
		hideSingleBase: false
	},
	_addItem: function (obj) {
		var label = document.createElement('label'),
		    checked = this._map.hasLayer(obj.layer),
		    input;

		if (obj.overlay) {
			input = document.createElement('input');
			input.type = 'checkbox';
			input.id = (obj.layer["layer_id"]);
			input.setAttribute("markercount",obj.layer["markercount"]);
			input.className = 'leaflet-control-layers-selector lmm-filter';
			input.defaultChecked = checked;
		} else {
			input = this._createRadioElement('leaflet-base-layers', checked);
		}

		input.layerId = L.stamp(obj.layer);

		L.DomEvent.on(input, 'click', this._onInputClick, this);

		var name = document.createElement('span');
		name.innerHTML = ' ' + obj.name;

		// Helps from preventing layer control flicker when checkboxes are disabled
		// https://github.com/Leaflet/Leaflet/issues/2771
		var holder = document.createElement('div');

		label.appendChild(holder);
		holder.appendChild(input);
		holder.appendChild(name);

		var container = obj.overlay ? this._overlaysList : this._baseLayersList;
		container.appendChild(label);

		this._checkDisabledLayers();
		return label;
	},
	_initLayout: function () {
		var className = 'leaflet-control-layers',
		    container = this._container = L.DomUtil.create('div', className),
		    collapsed = this.options.collapsed;

		// makes this work on IE touch devices by stopping it from firing a mouseout event when the touch is released
		container.setAttribute('aria-haspopup', true);

		L.DomEvent.disableClickPropagation(container);
		if (!L.Browser.touch) {
			L.DomEvent.disableScrollPropagation(container);
		}

		var form = this._form = L.DomUtil.create('form', className + '-list');

		if (collapsed) {
			this._map.on('click', this.collapse, this);

			if (!L.Browser.android) {
				L.DomEvent.on(container, {
					mouseenter: this.expand,
					mouseleave: this.collapse
				}, this);
			}
		}

		var link = this._layersLink = L.DomUtil.create('a', className + '-toggle lmm-filters-icon', container);
		link.href = '#';
		link.title = 'Layers';

		if (L.Browser.touch) {
			L.DomEvent
			    .on(link, 'click', L.DomEvent.stop)
			    .on(link, 'click', this.expand, this);
		} else {
			L.DomEvent.on(link, 'focus', this.expand, this);
		}

		// work around for Firefox Android issue https://github.com/Leaflet/Leaflet/issues/2033
		L.DomEvent.on(form, 'click', function () {
			setTimeout(L.bind(this._onInputClick, this), 0);
		}, this);

		// TODO keyboard accessibility

		if (!collapsed) {
			this.expand();
		}

		this._baseLayersList = L.DomUtil.create('div', className + '-base', form);
		this._separator = L.DomUtil.create('div', className + '-separator', form);
		this._overlaysList = L.DomUtil.create('div', className + '-overlays', form);

		container.appendChild(form);
	}
});
L.control.filters = function (baseLayers, overlays, options) {
	return new L.Control.Filters(baseLayers, overlays, options);
};

/*
dynamic markers list pagination
*/
jQuery(document).on('click','a.first-page',function(e,page_number){
	e.preventDefault();
	mmp_askForMarkersFromPagination(this);
});
function mmp_askForMarkersFromPagination(element,page_number,mapid){
		if(element != null){
			if(jQuery(element).hasClass('current-page')){
				return;
			}
		}
		if(!page_number){
			var page_number = jQuery(element).html();
		}
		if(!mapid){
			var mapid = jQuery(element).attr('data-mapid');
		}
		var per_page = jQuery("#markers_per_page_" + mapid).val();
		//info: consider the search field
		if(typeof jQuery('#search_markers_' + mapid).val()!= "undefined"){
			var search_text = jQuery('#search_markers_' + mapid).val();
		}else{
			var search_text = '';
		}
		if(typeof jQuery('#id').val()!= "undefined"){
			var id = jQuery('#id').val();
		}else{
			var id = jQuery('#' + mapid + '_id' ).val();
		}
		var mapname = jQuery('#lmm_listmarkers_table_' + mapid).attr('data-mapname');
		var layerlat = '';
		var layerlon = '';
		if(typeof window['mmp_cache_current_location_' + mapid] != "undefined"){
			var layerlat = window['mmp_cache_current_location_' + mapid].latitude;
			var layerlon = window['mmp_cache_current_location_' + mapid].longitude;
		}
		if(element != null){
			var page_link_element = element;
		}else{
			var page_link_element = '#pagination_' + mapid + ' .first-page:first';
		}
		jQuery.ajax({
			url:lmm_ajax_vars.lmm_ajax_url,
			data: {
				action: 'mapsmarker_ajax_actions_frontend',
				lmm_ajax_subaction: 'lmm_list_markers',
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				paged: page_number,
				order_by: jQuery('#'+mapid+'_orderby').val(),
				order: jQuery('#'+mapid+'_order').val(),
				multi_layer_map:jQuery('#'+mapid+'_multi_layer_map').val(),
				multi_layer_map_list:jQuery('#'+mapid+'_multi_layer_map_list').val(),
				markercount:jQuery('#'+mapid+'_markercount').val(),
				mapid:mapid,
				layerlat:layerlat,
				layerlon:layerlon,
				per_page: per_page,
				mapname: mapname,
				search_text: search_text,
				id:id
			},
			beforeSend: function(){
				if(mapid){
					jQuery('.lmm-filter').attr("disabled","disabled");
				}
				jQuery('#search_markers_' + mapid).addClass('searchtext_loading');
				jQuery('#pagination_' + mapid).html('<img src="'+   lmm_ajax_vars.lmm_ajax_leaflet_plugin_url	+'inc/img/paging-ajax-loader.gif"/>');
			},
			method:'POST',
			success: function(response){
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var res = JSON.parse(results);
				jQuery('#lmm_listmarkers_table_' + mapid).html(res.rows);
				jQuery('#lmm_listmarkers_table_' + mapid).append( '<tr id="pagination_row_'+mapid+'"><td colspan="2" style="text-align:center"><div class="tablenav"><div id="pagination_' + mapid +'" class="tablenav-pages">' + res.pager + '</div></div></td></tr>');
				jQuery(page_link_element).addClass('current-page');
				if(search_text == ''){
					try{
						window['mmp_calculate_total_markers_'+ mapid]();
					}catch(e){}
				}
				if(mapid){
					jQuery('.lmm-filter').removeAttr("disabled");
				}
				if(element != null){
					try{
						jQuery('html,body').animate({
						   scrollTop: jQuery("#lmm_" + mapid).offset().top //info: map ID in showmap.php
						}, 1000);
					}catch(n){
						jQuery('html,body').animate({
						   scrollTop: jQuery("#lmm_backend").offset().top //info: map ID in leaflet-layer.php
						}, 1000);
					}
				}
			}
		});
}

/*
dynamic markers per page
*/
jQuery(document).on('change','.lmm-per-page-input',function(e){
		var per_page = parseInt(jQuery(this).val());
		var mapid = jQuery(this).attr('data-mapid');
		if(typeof jQuery('#id').val()!= "undefined"){
			var id = jQuery('#id').val();
		}else{
			var id = jQuery( '#' + mapid + '_id' ).val();
		}
		var search_text = jQuery('#search_markers_' + mapid).val();
		if(!isNaN(per_page)){
			jQuery('.current-page').removeClass('current-page');
			var page_link_element = this;
			jQuery.ajax({
				url:lmm_ajax_vars.lmm_ajax_url,
				data: {
					action: 'mapsmarker_ajax_actions_frontend',
					lmm_ajax_subaction: 'lmm_list_markers',
					lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
					paged: 1,
					order_by: jQuery('#'+mapid+'_orderby').val(),
					order: jQuery('#'+mapid+'_order').val(),
					multi_layer_map:jQuery('#'+mapid+'_multi_layer_map').val(),
					multi_layer_map_list:jQuery('#'+mapid+'_multi_layer_map_list').val(),
					markercount:jQuery('#'+mapid+'_markercount').val(),
					mapid:mapid,
					per_page: per_page,
					search_text:search_text,
					id:id
				},
				beforeSend: function(){
					jQuery('#pagination_' + mapid).html('<img src="'+   lmm_ajax_vars.lmm_ajax_leaflet_plugin_url	+'inc/img/paging-ajax-loader.gif"/>');
					jQuery('#search_markers_' + mapid).addClass('searchtext_loading');
				},
				method:'POST',
				success: function(response){
					var results = response.replace(/^\s*[\r\n]/gm, '');
					var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
					var res = JSON.parse(results);
					if(typeof res.pager != 'undefined'){
						jQuery('#lmm_listmarkers_table_' + mapid).html(res.rows);
						jQuery('#lmm_listmarkers_table_' + mapid).append( '<tr id="pagination_row_'+mapid+'"><td colspan="2" style="text-align:center"><div class="tablenav"><div id="pagination_' + mapid+ '" class="tablenav-pages">' + res.pager + '</div></div></td></tr>');
					}else{
						jQuery('#pagination_' + mapid).html('');
					}
					jQuery(page_link_element).addClass('current-page');
					if(search_text==''){
						try{
							window['mmp_calculate_total_markers_'+ mapid]();
						}catch(e){}
					}
				}
			});
		}
});

/*
dynamic search
*/
/** info: debounce function to optimize the search field ajax requests. **/
function mmp_debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};
jQuery(document).on('keyup','.lmm-search-markers',mmp_debounce(mmp_askForMarkers,1000));
function mmp_askForMarkers(ev){
	if(jQuery(ev.target).val().length > 0){
		var inp = String.fromCharCode(ev.which);
		if (/[a-zA-Z0-9-_ ]/.test(inp) || ev.which == 13){
			var mapid = jQuery(ev.target).attr('data-mapid');
			var order_by = jQuery('#dropdown_'+mapid).attr('data-sortby');
			if(order_by == 'distance_current_position'){
		                if(typeof window['mmp_cache_current_location_' + mapid] != "undefined"){
		                        mmp_get_markers(ev.target, mapid, window['mmp_cache_current_location_' + mapid]);
		                }else{
		                        if(typeof window['locatecontrol_' + mapname] != "undefined"){
		                                window['locatecontrol_' + mapname].start();
		                                window[mapname].on('locationfound', function(location){
		                                        if(typeof window['mmp_cache_current_location_' + mapid] == "undefined"){
		                                                window['mmp_cache_current_location_' + mapid] = location;
		                                                mmp_get_markers(ev.target, mapid, location);
		                                        }
		                                });
		                        }else{
		                                window[mapname].locate().on('locationfound', function(location){
		                                        if(typeof window['mmp_cache_current_location_' + mapid] == "undefined"){
		                                                window['mmp_cache_current_location_' + mapid] = location;
		                                                mmp_get_markers(ev.target, mapid, location);
		                                        }
		                                });
		                        }
		                }
		        }else{
		                mmp_get_markers(ev.target, mapid);
		        }
		}
	}else if(jQuery(ev.target).val().length == 0){
		if(ev.which == 17 || ev.which == 8){
			var mapid = jQuery(ev.target).attr('data-mapid');
			var order_by = jQuery('#dropdown_'+mapid).attr('data-sortby');
                        if(order_by == 'distance_current_position'){
                                if(typeof window['mmp_cache_current_location_' + mapid] != "undefined"){
                                        mmp_get_markers(ev.target, mapid, window['mmp_cache_current_location_' + mapid]);
                                }else{
                                        if(typeof window['locatecontrol_' + mapname] != "undefined"){
                                                window['locatecontrol_' + mapname].start();
                                                window[mapname].on('locationfound', function(location){
                                                        if(typeof window['mmp_cache_current_location_' + mapid] == "undefined"){
                                                                window['mmp_cache_current_location_' + mapid] = location;
                                                                mmp_get_markers(ev.target, mapid, location);
                                                        }
                                                });
                                        }else{
                                                window[mapname].locate().on('locationfound', function(location){
                                                        if(typeof window['mmp_cache_current_location_' + mapid] == "undefined"){
                                                                window['mmp_cache_current_location_' + mapid] = location;
                                                                mmp_get_markers(ev.target, mapid, location);
                                                        }
                                                });
                                        }
                                }
                        }else{
                                mmp_get_markers(ev.target, mapid);
                        }

		}
	}
}
jQuery(document).on('click','.lmm-sort-by',mmp_debounce(mmp_askForMarkersFromDropdown,500));
function mmp_askForMarkersFromDropdown(ev, toggle){
    var mapid = jQuery(ev.target).parent().attr('data-mapid');
    var order_by = jQuery(ev.target).attr('data-sortby');
    var mapname = jQuery('#lmm_listmarkers_table_' + mapid).attr('data-mapname');
    if(order_by == 'distance_current_position'){
        if(typeof window['mmp_cache_current_location_' + mapid] != "undefined"){
            mmp_get_markers(ev.target, mapid, window['mmp_cache_current_location_' + mapid], toggle);
        }else{
            mmp_get_markers(ev.target, mapid, window[mapname].getCenter(), toggle);
            if(typeof window['locatecontrol_' + mapname] != "undefined"){
                window['locatecontrol_' + mapname].start();
                window[mapname].on('locationfound', function(location){
                    if(typeof window['mmp_cache_current_location_' + mapid] == "undefined"){
                        window['mmp_cache_current_location_' + mapid] = location;
                        mmp_get_markers(ev.target, mapid, location, toggle);
                    }
                });
            }else{
                window[mapname].locate().on('locationfound', function(location){
                    if(typeof window['mmp_cache_current_location_' + mapid] == "undefined"){
                        window['mmp_cache_current_location_' + mapid] = location;
                        mmp_get_markers(ev.target, mapid, location, toggle);
                    }
                });
            }
        }
    }else{
        mmp_get_markers(ev.target, mapid);
    }
}
function mmp_get_markers(element, mapid, location, toggle){
	toggle = typeof toggle !== 'undefined' ? toggle : true;
	var search_text = jQuery('#search_markers_' + mapid).val();
	if (typeof jQuery(element).attr('data-sortby') === 'undefined'){
		var order_by = jQuery('#dropdown_' + mapid).attr('data-sortby');
	} else {
		var order_by = jQuery(element).attr('data-sortby');
	}
	var mapname = jQuery('#lmm_listmarkers_table_' + mapid).attr('data-mapname');
	if(typeof jQuery('#id').val()!= "undefined"){
		var id = jQuery('#id').val();
	}else{
		var id = jQuery( '#' + mapid + '_id' ).val();
	}
	//info: in case of order by current center
	if(location){
		var layerlat = location.latitude;
		var layerlon = location.longitude;
	}
	if(jQuery(element).hasClass('lmm-sort-by') && toggle){
		if (jQuery(element).hasClass('up')){
			var order = 'desc';
			jQuery('.lmm-sort-by').removeClass('up');
			jQuery('.lmm-sort-by').removeClass('down');
			jQuery(element).removeClass('up');
			jQuery(element).addClass('down');
			jQuery('#dropdown_' + mapid).removeClass('up');
			jQuery('#dropdown_' + mapid).addClass('down');
		}else{
			var order = 'asc';
			jQuery('.lmm-sort-by').removeClass('up');
			jQuery('.lmm-sort-by').removeClass('down');
			jQuery(element).removeClass('down');
			jQuery(element).addClass('up');
			jQuery('#dropdown_' + mapid).removeClass('down');
			jQuery('#dropdown_' + mapid).addClass('up');
		}
	}else{
		if (jQuery('#dropdown_' + mapid).hasClass('up')){
			var order = 'asc';
		}else{
			var order = 'desc';
		}
	}
		var per_page = parseInt(jQuery('#markers_per_page_' + mapid).val());
		jQuery('.current-page').removeClass('current-page');
		var page_link_element = element;
		jQuery.ajax({
			url:lmm_ajax_vars.lmm_ajax_url,
			data: {
				action: 'mapsmarker_ajax_actions_frontend',
				lmm_ajax_subaction: 'lmm_list_markers',
				lmm_ajax_nonce: lmm_ajax_vars.lmm_ajax_nonce,
				paged: 1,
				multi_layer_map:jQuery('#'+mapid+'_multi_layer_map').val(),
				multi_layer_map_list:jQuery('#'+mapid+'_multi_layer_map_list').val(),
				markercount:jQuery('#'+mapid+'_markercount').val(),
				mapid:mapid,
				per_page: per_page,
				search_text:search_text,
				order_by: order_by,
				order: order,
				layerlat:layerlat,
				layerlon:layerlon,
				mapname: mapname,
				id:id
			},
			beforeSend: function(){
				jQuery('#pagination_' + mapid).html('<img src="'+   lmm_ajax_vars.lmm_ajax_leaflet_plugin_url	+'inc/img/paging-ajax-loader.gif"/>');
				jQuery('#search_markers_' + mapid).addClass('searchtext_loading');
			},
			method:'POST',
			success: function(response){
				var results = response.replace(/^\s*[\r\n]/gm, '');
				var results = results.match(/!!LMM-AJAX-START!!(.*[\s\S]*)!!LMM-AJAX-END!!/)[1];
				var res = JSON.parse(results);

				jQuery('#lmm_listmarkers_table_' + mapid).html(res.rows);
				if(res.no_pagination == true){
				if(search_text!='' && res.mcount==0){
						jQuery('#lmm_listmarkers_table_' + mapid).append( '<tr id="pagination_row_'+mapid+'"><td colspan="2" style="text-align:center"><div class="tablenav">'+ lmm_ajax_vars.lmm_ajax_text_no_results_found +'<div id="pagination_' + mapid +'" class="tablenav-pages" style="display:none;">' + res.pager + '</div></div></td></tr>');
					}else{
						jQuery('#lmm_listmarkers_table_' + mapid).append( '<tr id="pagination_row_'+mapid+'" style="display:none;"><td colspan="2" style="text-align:center"><div class="tablenav"><div id="pagination_' + mapid +'" class="tablenav-pages">' + res.pager + '</div></div></td></tr>');
					}
				}else{
					jQuery('#lmm_listmarkers_table_' + mapid).append( '<tr id="pagination_row_'+mapid+'"><td colspan="2" style="text-align:center"><div class="tablenav"><div id="pagination_' + mapid +'" class="tablenav-pages">' + res.pager + '</div></div></td></tr>');
				}
				jQuery(page_link_element).addClass('current-page');
				if(search_text!=''){
					//info: re-focus on the search field
					jQuery('#search_markers_' + mapid).focus();
					var tmpStr = jQuery('#search_markers_' + mapid).val();
					jQuery('#search_markers_' + mapid).val('');
					jQuery('#search_markers_' + mapid).val(tmpStr);
				}else{
					try{
						window['mmp_calculate_total_markers_'+ mapid]();
					}catch(e){}
				}
			}
		});
}
/**
 * Leaflet.MarkerCluster.Freezable 0.1.1+0285b6c
 * Sub-plugin for Leaflet.markercluster plugin; adds the ability to freeze clusters at a specified zoom.
 * (c) 2015-2017 Boris Seang
 * https://github.com/ghybs/Leaflet.MarkerCluster.Freezable
 * Last commit: Oct 26, 2016
 * License MIT
 */
(function (root, factory) {
    if (typeof define === "function" && define.amd) {
        define(["leaflet"], factory);
    } else if (typeof module === "object" && module.exports) {
        factory(require("leaflet"));
    } else {
        factory(root.L);
    }
}(this, function (L, undefined) {

L.MarkerClusterGroup.include({

	_originalOnAdd: L.MarkerClusterGroup.prototype.onAdd,

	onAdd: function (map) {
		var frozenZoom = this._zoom;

		this._originalOnAdd(map);

		if (this._frozen) {

			// Restore the specified frozenZoom if necessary.
			if (frozenZoom >= 0 && frozenZoom !== this._zoom) {
				// Undo clusters and markers addition to this._featureGroup.
				this._featureGroup.clearLayers();

				this._zoom = frozenZoom;

				this.addLayers([]);
			}

			// Replace the callbacks on zoomend and moveend events.
			map.off('zoomend', this._zoomEnd, this);
			map.off('moveend', this._moveEnd, this);
			map.on('zoomend moveend', this._viewChangeEndNotClustering, this);
		}
	},

	_originalOnRemove: L.MarkerClusterGroup.prototype.onRemove,

	onRemove: function (map) {
		map.off('zoomend moveend', this._viewChangeEndNotClustering, this);
		this._originalOnRemove(map);
	},

	disableClustering: function () {
		return this.freezeAtZoom(this._maxZoom + 1);
	},

	disableClusteringKeepSpiderfy: function () {
		return this.freezeAtZoom(this._maxZoom);
	},

	enableClustering: function () {
		return this.unfreeze();
	},

	unfreeze: function () {
		return this.freezeAtZoom(false);
	},

	freezeAtZoom: function (frozenZoom) {
		this._processQueue();

		var map = this._map;

		// If frozenZoom is not specified, true or NaN, freeze at current zoom.
		// Note: NaN is the only value which is not eaqual to itself.
		if (frozenZoom === undefined || frozenZoom === true || (frozenZoom !== frozenZoom)) {
			// Set to -1 if not on map, as the sign to freeze as soon as it gets added to a map.
			frozenZoom = map ? Math.round(map.getZoom()) : -1;
		} else if (frozenZoom === 'max') {
			// If frozenZoom is "max", freeze at MCG maxZoom + 1 (eliminates all clusters).
			frozenZoom = this._maxZoom + 1;
		} else if (frozenZoom === 'maxKeepSpiderfy') {
			// If "maxKeepSpiderfy", freeze at MCG maxZoom (eliminates all clusters but bottom-most ones).
			frozenZoom = this._maxZoom;
		}

		var requestFreezing = typeof frozenZoom === 'number';

		if (this._frozen) { // Already frozen.
			if (!requestFreezing) { // Unfreeze.
				this._unfreeze();
				return this;
			}
			// Just change the frozen zoom: go straight to artificial zoom.
		} else if (requestFreezing) {
			// Start freezing
			this._initiateFreeze();
		} else { // Not frozen and not requesting freezing => nothing to do.
			return this;
		}

		this._artificialZoomSafe(this._zoom, frozenZoom);
		return this;
	},

	_initiateFreeze: function () {
		var map = this._map;

		// Start freezing
		this._frozen = true;

		if (map) {
			// Change behaviour on zoomEnd and moveEnd.
			map.off('zoomend', this._zoomEnd, this);
			map.off('moveend', this._moveEnd, this);

			map.on('zoomend moveend', this._viewChangeEndNotClustering, this);
		}
	},

	_unfreeze: function () {
		var map = this._map;

		this._frozen = false;

		if (map) {
			// Restore original behaviour on zoomEnd.
			map.off('zoomend moveend', this._viewChangeEndNotClustering, this);

			map.on('zoomend', this._zoomEnd, this);
			map.on('moveend', this._moveEnd, this);

			// Animate.
			this._executeAfterUnspiderfy(function () {
				this._zoomEnd(); // Will set this._zoom at the end.
			}, this);
		}
	},

	_executeAfterUnspiderfy: function (callback, context) {
		// Take care of spiderfied markers!
		// The cluster might be removed, whereas markers are on fake positions.
		if (this._unspiderfy && this._spiderfied) {
			this.once('animationend', function () {
				callback.call(context);
			});
			this._unspiderfy();
			return;
		}

		callback.call(context);
	},

	_artificialZoomSafe: function (previousZoom, targetZoom) {
		this._zoom = targetZoom;

		if (!this._map || previousZoom === targetZoom) {
			return;
		}

		this._executeAfterUnspiderfy(function () {
			this._artificialZoom(previousZoom, targetZoom);
		}, this);
	},

	_artificialZoom: function (previousZoom, targetZoom) {
		if (previousZoom < targetZoom) {
			// Make as if we had instantly zoomed in from previousZoom to targetZoom.
			this._animationStart();
			this._topClusterLevel._recursivelyRemoveChildrenFromMap(
				this._currentShownBounds, previousZoom, this._getExpandedVisibleBounds()
			);
			this._animationZoomIn(previousZoom, targetZoom);

		} else if (previousZoom > targetZoom) {
			// Make as if we had instantly zoomed out from previousZoom to targetZoom.
			this._animationStart();
			this._animationZoomOut(previousZoom, targetZoom);
		}
	},

	_viewChangeEndNotClustering: function () {
		var fg = this._featureGroup,
		    newBounds = this._getExpandedVisibleBounds(),
		    targetZoom = this._zoom;

		// Remove markers and bottom clusters outside newBounds, unless they come
		// from a spiderfied cluster.
		fg.eachLayer(function (layer) {
			if (!newBounds.contains(layer._latlng) && layer.__parent && layer.__parent._zoom < targetZoom) {
				fg.removeLayer(layer);
			}
		});

		// Add markers and bottom clusters in newBounds.
		this._topClusterLevel._recursively(newBounds, -1, targetZoom,
			function (c) { // Add markers from each cluster of lower zoom than targetZoom
				if (c._zoom === targetZoom) { // except targetZoom
					return;
				}

				var markers = c._markers,
				    i = 0,
				    marker;

				for (; i < markers.length; i++) {
					marker = c._markers[i];

					if (!newBounds.contains(marker._latlng)) {
						continue;
					}

					fg.addLayer(marker);
				}
			},
			function (c) { // Add clusters from targetZoom.
				c._addToMap();
			}
		);
	},

	_originalZoomOrSpiderfy: L.MarkerClusterGroup.prototype._zoomOrSpiderfy,

	_zoomOrSpiderfy: function (e) {
		if (this._frozen && this.options.spiderfyOnMaxZoom) {
			e.layer.spiderfy();
			if (e.originalEvent && e.originalEvent.keyCode === 13) {
				map._container.focus();
			}
		} else {
			this._originalZoomOrSpiderfy(e);
		}
	}

});



}));

//# sourceMappingURL=leaflet.markercluster.freezable-src.map

/*
 * Leaflet.EdgeBuffer, https://github.com/TolonUK/Leaflet.EdgeBuffer (v1.0.5)
 * Last Commit: 7.5.2017
 * License: MIT
*/
(function (factory, window) {
  // define an AMD module that relies on 'leaflet'
  if (typeof define === 'function' && define.amd) {
    define(['leaflet'], factory);

  // define a Common JS module that relies on 'leaflet'
  } else if (typeof exports === 'object') {
    module.exports = factory(require('leaflet'));
  }

  // attach your plugin to the global 'L' variable
  if (typeof window !== 'undefined' && window.L && !window.L.EdgeBuffer) {
    factory(window.L);
  }
}(function (L) {
  L.EdgeBuffer = {
    previousMethods: {
      getTiledPixelBounds: L.GridLayer.prototype._getTiledPixelBounds
    }
  };

  L.GridLayer.include({

    _getTiledPixelBounds : function(center, zoom, tileZoom) {
      var pixelBounds = L.EdgeBuffer.previousMethods.getTiledPixelBounds.call(this, center, zoom, tileZoom);

      // Default is to buffer one tiles beyond the pixel bounds (edgeBufferTiles = 1).
      var edgeBufferTiles = 1;
      if ((this.options.edgeBufferTiles !== undefined) && (this.options.edgeBufferTiles !== null)) {
        edgeBufferTiles = this.options.edgeBufferTiles;
      }

      if (edgeBufferTiles > 0) {
        var pixelEdgeBuffer = edgeBufferTiles * this.options.tileSize;
        pixelBounds = new L.Bounds(pixelBounds.min.subtract([pixelEdgeBuffer, pixelEdgeBuffer]), pixelBounds.max.add([pixelEdgeBuffer, pixelEdgeBuffer]));
      }
      return pixelBounds;
    }
  });

}, window));
