YUI = yui-compressor
YUIOPTS = --type css

CLC = closure-compiler
CLCOPTS = --compilation_level ADVANCED_OPTIMIZATIONS

# Match CSS files, except those with suffix `.min.css`.
CSS = $(filter-out %.min.css,$(wildcard css/*.css css/**/*.css))
CSSMIN = $(CSS:%.css=%.min.css)

# Match JS files, except those with suffix `.min.js`.
JS = $(filter-out %.min.js,$(wildcard js/*.js js/**/*.js))
JSMIN = $(JS:%.js=%.min.js)

.PHONY: all css js clean clean-css clean-js
all: css js
clean: clean-css clean-js
css: $(CSSMIN)
js: $(JSMIN)

%.min.css: %.css
	@printf -- '-- Miniying %s ...\n' "$<"
	$(YUI) $(YUIOPTS) $< -o $@

%.min.js: %.js
	@printf -- '-- Miniying %s ...\n' "$<"
	$(CLC) $(CLCOPTS) $< --js_output_file $@

clean-css:
	@printf -- '-- Cleaning up minified CSS ...\n' "$<"
	rm -f -- $(CSSMIN)

clean-js:
	@printf -- '-- Cleaning up minified JS ...\n' "$<"
	rm -f -- $(JSMIN)
