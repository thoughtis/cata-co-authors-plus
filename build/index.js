!function(){"use strict";var e,t={662:function(e,t,n){var r=window.wp.element,o=window.wp.plugins,u=window.wp.data,i=window.wp.editPost,a=window.wp.apiFetch,c=n.n(a);function s(){const e=(0,u.useDispatch)("core/notices"),{getCurrentPost:t}=(0,u.useSelect)("core/editor"),[n,o]=(0,r.useState)(""),[a,s]=(0,r.useState)(null),[l,f]=(0,r.useState)("");function p(e){Array.isArray(e)&&0!==e.length?f(e[0].profile.link):(f(""),h(new Error(`Author ${n} not found`)))}function h(t){e.createErrorNotice(t.message,{isDismissible:!0})}return(0,r.useEffect)((()=>{s(t())}),[]),(0,r.useEffect)((()=>{o((null==a?void 0:a.slug)||"")}),[a]),(0,r.useEffect)((()=>{"string"==typeof n&&""!==n&&c()({path:`/wp/v2/coauthors?slug=${n}`}).then(p).catch(h)}),[n]),(0,r.createElement)(i.PluginDocumentSettingPanel,{title:"Profile URL",name:"cata-cap-guest-author-url"},l&&0<l.length&&(0,r.createElement)("p",null,(0,r.createElement)("a",{target:"_blank",href:l},l)))}(0,o.registerPlugin)("cata-cap-guest-author",{render:function(){const{removeEditorPanel:e}=(0,u.useDispatch)("core/edit-post");return(0,r.useEffect)((()=>{e("post-status")}),[]),(0,r.createElement)(s,null)}})}},n={};function r(e){var o=n[e];if(void 0!==o)return o.exports;var u=n[e]={exports:{}};return t[e](u,u.exports,r),u.exports}r.m=t,e=[],r.O=function(t,n,o,u){if(!n){var i=1/0;for(l=0;l<e.length;l++){n=e[l][0],o=e[l][1],u=e[l][2];for(var a=!0,c=0;c<n.length;c++)(!1&u||i>=u)&&Object.keys(r.O).every((function(e){return r.O[e](n[c])}))?n.splice(c--,1):(a=!1,u<i&&(i=u));if(a){e.splice(l--,1);var s=o();void 0!==s&&(t=s)}}return t}u=u||0;for(var l=e.length;l>0&&e[l-1][2]>u;l--)e[l]=e[l-1];e[l]=[n,o,u]},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,{a:t}),t},r.d=function(e,t){for(var n in t)r.o(t,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={826:0,431:0};r.O.j=function(t){return 0===e[t]};var t=function(t,n){var o,u,i=n[0],a=n[1],c=n[2],s=0;if(i.some((function(t){return 0!==e[t]}))){for(o in a)r.o(a,o)&&(r.m[o]=a[o]);if(c)var l=c(r)}for(t&&t(n);s<i.length;s++)u=i[s],r.o(e,u)&&e[u]&&e[u][0](),e[u]=0;return r.O(l)},n=self.webpackChunkcata_co_authors_plus=self.webpackChunkcata_co_authors_plus||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))}();var o=r.O(void 0,[431],(function(){return r(662)}));o=r.O(o)}();