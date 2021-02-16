!function(e){var t={};function r(n){if(t[n])return t[n].exports;var o=t[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,r),o.l=!0,o.exports}r.m=e,r.c=t,r.d=function(e,t,n){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)r.d(n,o,function(t){return e[t]}.bind(null,o));return n},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="",r(r.s=15)}({15:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=r(16),o=function(){function e(e){this.stringToProcess=e,this.parser=new n.Parser(this.stringToProcess)}return e.prototype.Compile=function(){return this.parser.Parse()},e}();t.FormulaCompiler=o,window.FormulaCompiler=o},16:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=r(17),o=function(){function e(e){this.stringToProcess=e,this.code="",this.header="",this.footer="",this.variableCount=0,this.lexer=new n.Lexer(this.stringToProcess),this.currentToken=this.lexer.GetNextToken()}return e.prototype.eat=function(e){if(this.currentToken.Type!=e)throw"Invalid Formula";this.currentToken=this.lexer.GetNextToken()},e.prototype.Parse=function(){return this.Expr()},e.prototype.Expr=function(){for(;null!=this.currentToken;)this.currentToken.Type!=n.TokenType.Method?this.code+=this.currentToken.Value:this.code+=this.Method(),this.eat(this.currentToken.Type);return this.CreateRootPromise()},e.prototype.Method=function(){var e=this.currentToken.Value;this.eat(n.TokenType.Method);for(var t=-1;null!=this.currentToken&&(this.currentToken.Type!=n.TokenType.RParen||t>0);)this.currentToken.Type==n.TokenType.RParen&&t--,this.currentToken.Type==n.TokenType.LParen&&t++,this.currentToken.Type==n.TokenType.Method?e+=this.Method():e+=this.currentToken.Value,this.eat(this.currentToken.Type);e+=")";var r="result"+this.variableCount;return this.header+=e+".then(function("+r+"){",this.variableCount++,this.footer+="})",r},e.prototype.CreateRootPromise=function(){return"new Promise(function(sfInternalResolve){\n            "+this.header+"\n                sfInternalResolve("+this.code+");\n            "+this.footer+"\n        });"},e}();t.Parser=o},17:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n,o=r(18),i=function(){function e(e){this.stringToProcess=e,this.stringBuffer="",this.currentIndex=-1}return e.prototype.GetNextToken=function(){if(this.currentIndex++,this.stringToProcess.length<=this.currentIndex)return this.AnalizeToken();var e=this.stringToProcess[this.currentIndex];if(this.IsWhiteSpace(e))return this.stringBuffer.length>0?(this.currentIndex--,this.AnalizeToken()):(this.stringBuffer+=" ",this.AnalizeToken());if(this.IsQuote(e)){var t=this.CreateQuotedString();return this.stringBuffer="",t}return this.IsParentheses(e)?this.stringBuffer.length>0?(this.currentIndex--,this.AnalizeToken()):"("==e?new o.Token(n.LParen,"("):new o.Token(n.RParen,")"):this.IsSymbol(e)?this.stringBuffer.length>0?(this.currentIndex--,this.AnalizeToken()):new o.Token(n.Symbol,e):(this.stringBuffer+=e,this.GetNextToken())},e.prototype.AnalizeToken=function(){if(0==this.stringBuffer.length)return null;var e={Type:n.Whatever,Value:this.stringBuffer};return"Remote.Get"!=this.stringBuffer&&"Remote.Post"!=this.stringBuffer||(e.Type=n.Method),this.stringBuffer="",e},e.prototype.IsWhiteSpace=function(e){return" "==e||"\r"==e||"\t"==e||"\n"==e},e.prototype.IsQuote=function(e){return"'"==e||'"'==e},e.prototype.CreateQuotedString=function(){var e=this.stringToProcess[this.currentIndex];for(this.stringBuffer+=e,this.currentIndex++;this.currentIndex<this.stringToProcess.length&&(this.stringToProcess[this.currentIndex]!=e||"\\"==this.stringToProcess[this.currentIndex-1]);)this.stringBuffer+=this.stringToProcess[this.currentIndex],this.currentIndex++;return this.stringToProcess[this.currentIndex]==e&&(this.stringBuffer+=e),new o.Token(n.String,this.stringBuffer)},e.prototype.IsParentheses=function(e){return"("==e||")"==e},e.prototype.IsSymbol=function(e){return[",","&","|",";","+","-","/","*"].indexOf(e)>=0},e}();t.Lexer=i,function(e){e[e.Method=1]="Method",e[e.Whatever=2]="Whatever",e[e.Comma=3]="Comma",e[e.LParen=4]="LParen",e[e.RParen=5]="RParen",e[e.String=6]="String",e[e.Symbol=7]="Symbol"}(n=t.TokenType||(t.TokenType={}))},18:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=function(){return function(e,t){this.Type=e,this.Value=t}}();t.Token=n}});
//# sourceMappingURL=formulacompiler_bundle.js.map