This release adds a feature: _Getopt_ defintion strings now allow for noting positional arguments using the `#argname` and `#argname?` notation, optionally with an argument description. These are ignored by the _GetoptParser_ for purposes of value-discovery, but the _Help_ class *does* use them to auto-generate usage lines.
This release modifies the testing structure and updates other support files.

