(()=>{"use strict";var e,t={499:(e,t,r)=>{const n=window.React,o=window.wp.plugins,a=window.wp.data,s=window.wp.element,u=window.wp.editPost,i=window.wp.apiFetch;var c=r.n(i);function l(){const e=(0,a.useDispatch)("core/notices"),{getCurrentPost:t}=(0,a.useSelect)("core/editor"),[r,o]=(0,s.useState)(""),[i,l]=(0,s.useState)(null),[p,f]=(0,s.useState)("");function h(e){Array.isArray(e)&&0!==e.length?f(e[0].profile.link):(f(""),d(new Error(`Author ${r} not found`)))}function d(t){e.createErrorNotice(t.message,{isDismissible:!0})}return(0,s.useEffect)((()=>{l(t())}),[]),(0,s.useEffect)((()=>{o(i?.slug||"")}),[i]),(0,s.useEffect)((()=>{"string"==typeof r&&""!==r&&c()({path:`/wp/v2/coauthors?slug=${r}`}).then(h).catch(d)}),[r]),(0,n.createElement)(u.PluginDocumentSettingPanel,{title:"Profile URL",name:"cata-cap-guest-author-url"},p&&0<p.length&&(0,n.createElement)("p",null,(0,n.createElement)("a",{target:"_blank",href:p},p)))}(0,o.registerPlugin)("cata-cap-guest-author",{render:function(){const{removeEditorPanel:e}=(0,a.useDispatch)("core/edit-post");return(0,s.useEffect)((()=>{e("post-status")}),[]),(0,n.createElement)(l,null)}})}},r={};function n(e){var o=r[e];if(void 0!==o)return o.exports;var a=r[e]={exports:{}};return t[e](a,a.exports,n),a.exports}n.m=t,e=[],n.O=(t,r,o,a)=>{if(!r){var s=1/0;for(l=0;l<e.length;l++){for(var[r,o,a]=e[l],u=!0,i=0;i<r.length;i++)(!1&a||s>=a)&&Object.keys(n.O).every((e=>n.O[e](r[i])))?r.splice(i--,1):(u=!1,a<s&&(s=a));if(u){e.splice(l--,1);var c=o();void 0!==c&&(t=c)}}return t}a=a||0;for(var l=e.length;l>0&&e[l-1][2]>a;l--)e[l]=e[l-1];e[l]=[r,o,a]},n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={57:0,350:0};n.O.j=t=>0===e[t];var t=(t,r)=>{var o,a,[s,u,i]=r,c=0;if(s.some((t=>0!==e[t]))){for(o in u)n.o(u,o)&&(n.m[o]=u[o]);if(i)var l=i(n)}for(t&&t(r);c<s.length;c++)a=s[c],n.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return n.O(l)},r=globalThis.webpackChunkcata_co_authors_plus=globalThis.webpackChunkcata_co_authors_plus||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var o=n.O(void 0,[350],(()=>n(499)));o=n.O(o)})();