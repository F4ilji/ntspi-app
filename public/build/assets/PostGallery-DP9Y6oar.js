import{i as g,o as r,c as n,b as o,j as p,F as a,d as b,t as l,r as _,l as v,k as x,a as f}from"./app-lT3z3TR3.js";import"./SearchModal-YI3bdWA9.js";import{_ as h}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{F as w}from"./v3-B5UP5GZw.js";const k={name:"PostAuthorsList",components:{Link:g},data(){return{}},methods:{textLimit(s,e){if(s.length>e){let t;return t=s.substring(0,e),t+"..."}return s}},props:{authors:{type:Array}}},L={class:"col-start-2"},P={class:"hs-tooltip inline-block"},z={type:"button",class:"hs-tooltip-toggle underline hover:text-blue-400 duration-300"},$={class:"hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute duration-300 invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-neutral-700",role:"tooltip"};function j(s,e,t,m,i,d){return r(),n("div",L,[o("div",P,[o("button",z,[e[1]||(e[1]=p(" Над статьей работали ")),o("span",$,[(r(!0),n(a,null,b(t.authors,c=>(r(),n(a,null,[p(l(c)+" ",1),e[0]||(e[0]=o("br",null,null,-1))],64))),256))])])])])}const tt=h(k,[["render",j]]),C={name:"PostTimeRead",components:{Link:g},data(){return{}},methods:{textLimit(s,e){if(s.length>e){let t;return t=s.substring(0,e),t+"..."}return s}},props:{time:{type:String}}},F={class:"block"};function S(s,e,t,m,i,d){return r(),n("span",F,"Чтение займет "+l(t.time)+" Минуты",1)}const et=h(C,[["render",S]]),T={name:"PostGallery",components:{Link:g,FsLightbox:w},data(){return{toggler:!1,slide:1,domainPath:null}},methods:{textLimit(s,e){if(s.length>e){let t;return t=s.substring(0,e),t+"..."}return s},openLightboxOnSlide:function(s){this.slide=s,this.toggler=!this.toggler}},mounted(){this.domainPath=window.location.origin},props:{images:{type:Array},title:{type:String}}},B={key:0,class:"grid gap-4 border-t border-gray-300 py-4"},N={class:"relative"},V=["src"],A={class:"absolute inset-x-0 bottom-5 flex flex-col justify-center items-center"},G={class:"text-white text-sm lg:text-xl text-center"},O={class:"text-gray-300 text-sm"},R={id:"modal-post-gallery",class:"hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none",role:"dialog",tabindex:"-1","aria-labelledby":"modal-post-gallery-label"},D={class:"hs-overlay-open:mt-0 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-10 opacity-0 transition-all max-w-full max-h-full h-full md:hs-overlay-open:mt-10 md:mt-0 md:max-w-lg md:max-h-none md:h-auto md:mx-auto"},E={class:"flex flex-col bg-white pointer-events-auto max-w-full max-h-full h-full md:max-w-lg md:max-h-none md:h-auto md:border md:rounded-xl md:shadow-sm dark:bg-neutral-800 md:dark:border-neutral-700"},M={class:"flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700"},q={id:"modal-post-gallery-label",class:"font-bold text-gray-800 dark:text-white"},H={class:"p-4 overflow-y-auto"},I={class:"grid grid-cols-2 sm:grid-cols-3 gap-3"},J={class:"group block relative overflow-hidden rounded-lg"},K=["onClick","src"];function Q(s,e,t,m,i,d){const c=_("FsLightbox");return r(),n(a,null,[t.images.length?(r(),n("div",B,[o("div",N,[o("img",{loading:"lazy",class:"filter brightness-[0.8] w-full max-h-[500px] object-cover rounded-lg hover:opacity-95 hover:duration-200 transition",src:"/storage/"+t.images[0],"data-hs-overlay":"#modal-post-gallery",alt:""},null,8,V),o("div",A,[o("p",G,l(t.title),1),o("span",O,l(t.images.length)+" фотографий",1)])])])):v("",!0),o("div",R,[o("div",D,[o("div",E,[o("div",M,[o("h3",q," Галлерея: "+l(t.title),1),e[0]||(e[0]=x('<button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#modal-post-gallery"><span class="sr-only">Close</span><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button>',1))]),o("div",H,[o("div",I,[(r(!0),n(a,null,b(t.images,(u,y)=>(r(),n("div",J,[o("img",{loading:"lazy",onClick:U=>d.openLightboxOnSlide(y+1),class:"w-full size-40 object-cover bg-gray-100 rounded-lg",src:"/storage/"+u},null,8,K),e[1]||(e[1]=x('<div class="absolute bottom-1 end-1 opacity-0 group-hover:opacity-100 transition"><div class="flex items-center gap-x-1 py-1 px-2 bg-white border border-gray-200 text-gray-800 rounded-lg"><svg class="shrink-0 size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg><span class="text-xs">Открыть</span></div></div>',1))]))),256))])])])])]),f(c,{class:"",slide:i.slide,toggler:i.toggler,sources:t.images.map(u=>i.domainPath+"/storage/"+u)},null,8,["slide","toggler","sources"])],64)}const ot=h(T,[["render",Q]]);export{ot as P,et as a,tt as b};