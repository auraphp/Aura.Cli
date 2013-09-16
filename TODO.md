- Add progress helper per notes from harikt, as a trait:
  <https://github.com/harikt/Aura.Cli/commit/0b1d0278cc0ec599a70b260073fa895f937d9611>

- Instead of always allowing multiples for options, add a * notation

- Instead of ['foo' => 'f'] for aliasing options, use a comma in the name, and
  then do ['foo,f' => "Option description"] for automated help text.
  
Thus:

    foo         -- param rejected
    foo:        -- param required
    foo::       -- param optional
    foo*        -- may be passed multiple times (arrayed or counted)
    foo,f       -- aliased to -f
    foo*::,f    -- multiple times, param optional, aliased to -f*::
    ['foo*::,f' => 'Description for this option']
    
Instead of doing integer counts, always use an array, and append the value
'true' to the array.  If you want a count you can count($values). And when
it the option is a multiple, force it to an array right away.

