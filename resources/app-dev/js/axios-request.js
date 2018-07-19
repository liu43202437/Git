var __DEV__ = 1;
var host = (__DEV__) ? 'http://yan.eeseetech.cn' : '';

var axiosUrlParams = function (name) {
  var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
  var r = window.location.search.substr(1).match(reg);
  if (r != null) return unescape(r[2]);
  return null;
}

var apiRequest = function (url, data) {
  var req = Object.assign({}, data, {
    sid: axiosUrlParams('sid')
  });
  return axios({
    method: 'post',
    url: host + url,
    data: qsStringify(req),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  }).then(res => {
    if (res.status === 200) {
      return res.data;
    } else {
      return Promise.reject(res);
    }
  });
}

// Extracted from QS lib

function isBuffer(obj) {
  if (obj === null || typeof obj === 'undefined') {
    return false;
  }

  return !!(obj.constructor && obj.constructor.isBuffer && obj.constructor.isBuffer(obj));
}

var defaultFormatter = function (v) {
  return v;
}

var arrayPrefixGenerators = {
  brackets: function brackets(prefix) { // eslint-disable-line func-name-matching
    return prefix + '[]';
  },
  indices: function indices(prefix, key) { // eslint-disable-line func-name-matching
    return prefix + '[' + key + ']';
  },
  repeat: function repeat(prefix) { // eslint-disable-line func-name-matching
    return prefix;
  }
};

var defaultSerializeDate = function (date) { // eslint-disable-line func-name-matching
  return Date.prototype.toISOString.call(date);
}

var stringify = function stringify( // eslint-disable-line func-name-matching
  object,
  prefix,
  generateArrayPrefix = arrayPrefixGenerators['indices'],
  strictNullHandling = false,
  skipNulls = false,
  encoder = null,
  filter,
  sort = null,
  allowDots = false,
  serializeDate = defaultSerializeDate,
  formatter = defaultFormatter,
  encodeValuesOnly = false
) {
  var obj = object;

  if (typeof filter === 'function') {
    obj = filter(prefix, obj);
  } else if (obj instanceof Date) {
    obj = serializeDate(obj);
  } else if (obj === null) {
    if (strictNullHandling) {
      return encoder && !encodeValuesOnly ? encoder(prefix, defaultEncoder) : prefix;
    }

    obj = '';
  }

  if (typeof obj === 'string' || typeof obj === 'number' || typeof obj === 'boolean' || isBuffer(obj)) {
    if (encoder) {
      var keyValue = encodeValuesOnly ? prefix : encoder(prefix, defaultEncoder);
      return [formatter(keyValue) + '=' + formatter(encoder(obj, defaultEncoder))];
    }
    return [formatter(prefix) + '=' + formatter(String(obj))];
  }

  var values = [];

  if (typeof obj === 'undefined') {
    return values;
  }

  var objKeys;
  if (Array.isArray(filter)) {
    objKeys = filter;
  } else {
    var keys = Object.keys(obj);
    objKeys = sort ? keys.sort(sort) : keys;
  }

  for (var i = 0; i < objKeys.length; ++i) {
    var key = objKeys[i];

    if (skipNulls && obj[key] === null) {
      continue;
    }

    if (Array.isArray(obj)) {
      values = values.concat(stringify(
        obj[key],
        generateArrayPrefix(prefix, key),
        generateArrayPrefix,
        strictNullHandling,
        skipNulls,
        encoder,
        filter,
        sort,
        allowDots,
        serializeDate,
        formatter,
        encodeValuesOnly
      ));
    } else {
      values = values.concat(stringify(
        obj[key],
        prefix + (allowDots ? '.' + key : '[' + key + ']'),
        generateArrayPrefix,
        strictNullHandling,
        skipNulls,
        encoder,
        filter,
        sort,
        allowDots,
        serializeDate,
        formatter,
        encodeValuesOnly
      ));
    }
  }

  return values;
};

var qsStringify = function (object) {
  var obj = object;
  var objKeys;
  var keys = [];

  if (typeof obj !== 'object' || obj === null) {
    return '';
  }

  if (!objKeys) {
    objKeys = Object.keys(obj);
  }

  for (var i = 0; i < objKeys.length; ++i) {
    var key = objKeys[i];

    if (obj[key] === null) {
      continue;
    }

    keys = keys.concat(stringify(
      obj[key],
      key
    ));
  }

  var joined = keys.join('&');
  return joined.length > 0 ? joined : '';
}