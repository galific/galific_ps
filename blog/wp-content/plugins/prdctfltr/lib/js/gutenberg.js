var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

!function (e, t, o, i) {

	wp.hooks.addFilter('blocks.registerBlockType', 'prdctfltr-attribute', function (e) {

		if ( e.name == 'woocommerce/products' ) {
			return void 0 !== e.attributes && ( e.attributes = Object.assign( e.attributes, {
				prdctfltr: {
					type: 'string',
					default: ''
				},
				preset: {
					type: 'string',
					default: ''
				},
				ajax: {
					type: 'string',
					default: ''
				},
				pagination: {
					type: 'string',
					default: ''
				}
			} ) ), e
		}
		return e;

	} );

	var s = wp.compose.createHigherOrderComponent( function (e) {

		var presets = typeof prdctfltr.presets !== 'undefined' ? prdctfltr.presets : {};

		return (function (t) {

			var i = wp.element.createElement,
				s = (wp.i18n.__, wp.editor.BlockControls, wp.editor.InspectorControls),
				l = (wp.components.Button, wp.components.TextControl, wp.components.SelectControl),
				n = wp.components.CheckboxControl,
				c = (wp.components.PanelRow, wp.components.PanelBody),
				p = (wp.components.Popover, Object.assign({}, t));
			p.key = 'blockOptions';
			var d = i(e, p),
				r = (t.isSelected, t.attributes, t.attributes.prdctfltr ? t.attributes.prdctfltr : ''),
				r1 = (t.isSelected, t.attributes, t.attributes.preset ? t.attributes.preset : ''),
				r2 = (t.isSelected, t.attributes, t.attributes.ajax ? t.attributes.ajax : ''),
				r3 = (t.isSelected, t.attributes, t.attributes.pagination ? t.attributes.pagination : ''),
				v = !1;

			if ( t.name !== 'woocommerce/products' ) {
				return [ d ];
			}

			return [
				d,
				i(s, {
					key: 'inspector'
					}
					,i(c, {
						title: 'Product Filter',
						initialOpen: v
					}
					,i('div', {
						className: 'components-product-filters'
					}
					,i(l, {
							label: o.__('Use filter'),
							value: void 0 !== r ? r : '',
							options: [
								{
									label: o.__('No'),
									value: 'no'
								},
								{
									label: o.__('On top'),
									value: 'yes'
								},
								{
									label: o.__('From widget'),
									value: 'widget'
								}
							],
							onChange: function(e) {
								t.setAttributes( {
									prdctfltr: e
								} );
							}
						} )
						,i(l, {
							label: o.__('Select preset'),
							value: void 0 !== r1 ? r1 : '',
							options: presets,
							onChange: function(e) {
								t.setAttributes( {
									preset: e
								} );
							}
						} )
						,i(l, {
							label: o.__('AJAX'),
							value: void 0 !== r2 ? r2 : '',
							options: [
								{
									label: o.__('No'),
									value: 'no'
								},
								{
									label: o.__('Yes'),
									value: 'yes'
								}
							],
							onChange: function(e) {
								t.setAttributes( {
									ajax: e
								} );
							}
						} )
						,i(l, {
							label: o.__('Pagination'),
							value: void 0 !== r3 ? r3 : '',
							options: [
								{
									label: o.__('No'),
									value: 'no'
								},
								{
									label: o.__('Yes'),
									value: 'yes'
								},
								{
									label: o.__('Load more'),
									value: 'loadmore'
								},
								{
									label: o.__('Infinite load'),
									value: 'infinite'
								}
							],
							onChange: function(e) {
								t.setAttributes( {
									pagination: e
								} );
							}
						} )
					) )
				)
			];

		} );

	}, 'withInspectorControls' );

	wp.hooks.addFilter( 'editor.BlockEdit', 'product-filter/inspector', s );

	wp.hooks.addFilter( 'blocks.getSaveContent.extraProps', 'product-filter/save', function (props, blockType,x ) {
		if(blockType.name === 'woocommerce/products') {
			if ( props.children.includes('prdctfltr') || x.prdctfltr == 'yes' || x.prdctfltr == 'widget' ) {
				var str = get_modified(x);
				props.children = props.children.substr(0, props.children.length-1)+str;
			}
			return props;
		}
		return props;
	} );

	function get_modified(props) {
		var _props = props,
			prdctfltr = _props.prdctfltr,
			preset = _props.preset,
			ajax = _props.ajax,
			pagination = _props.pagination;

		var shortcode_atts = new Map();

		shortcode_atts.set('prdctfltr', prdctfltr);
		shortcode_atts.set('preset', preset);
		shortcode_atts.set('ajax', ajax);
		shortcode_atts.set('pagination', pagination);

		var shortcode = '';
		var _iteratorNormalCompletion4 = true;
		var _didIteratorError4 = false;
		var _iteratorError4 = undefined;

		try {
			for (var _iterator4 = shortcode_atts[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
				var _ref5 = _step4.value;

				var _ref6 = _slicedToArray(_ref5, 2);

				var key = _ref6[0];
				var value = _ref6[1];

				shortcode += ' ' + key + '="' + value + '"';
			}
		} catch (err) {
			_didIteratorError4 = true;
			_iteratorError4 = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion4 && _iterator4.return) {
					_iterator4.return();
				}
			} finally {
				if (_didIteratorError4) {
					throw _iteratorError4;
				}
			}
		}

		shortcode += ']';

		return shortcode;

	}


}(window.wp.hooks, window.wp.blocks, window.wp.i18n, window.wp.element);