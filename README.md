# WebExcess.InheritProperties

This package allows NodeType Property Inheritance.

**Important:** You need the PR versions of [Flow](https://github.com/neos/flow-development-collection/pull/750) and [Neos](https://github.com/neos/neos-development-collection/pull/1292).


## Examples

**Copy**

    # Copy `original_text` configuration into `text`
    'text < original_text': []

**Merge**

    # Copy `original_text` configuration into `text` and merge it with your custom placeholder text
    'text < original_text':
      ui:
        aloha:
          placeholder: 'Enter you text here.'

**Remove**

	# Remove the property `original_text`
	'original_text >': []
