uri:
noun: foo
verb: bar

method: get

ttl: 300

validation: FALSE

process:
processor: field
meta:
name:
processor: varStore
meta:
operation: fetch
var: myVarName
value:
processor: varUri
meta:
index: 1

output:
- response
- email:
format: json
destination:
- john@naala.com.au
- foo@bar.com
- xml:
destination:
-www.foo.com
-www.bar.com
- json:
destination:
- www.wotnot.com
