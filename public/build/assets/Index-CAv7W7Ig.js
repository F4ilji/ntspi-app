import{M as f}from"./MainNavbar-5-nqk6c_.js";import{B as y,C as w,_ as k}from"./SearchModal-YI3bdWA9.js";import{i as _,r as i,c as o,a as n,b as e,h as B,q as M,y as I,p as C,k as N,w as j,z as F,F as r,o as a,d,j as D,t as g}from"./app-lT3z3TR3.js";import{M as V}from"./MainPageNavbar-BfM50q-7.js";import{_ as z}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css                                                                   */const S={name:"Index",data(){return{searchInput:this.searchRequest}},components:{BaseIcon:y,MainPageNavBar:V,ClientFooterDown:w,MainNavbar:f,Link:_},props:["educationalGroups","mainSections","searchRequest","navigation"],methods:{search:k.debounce(function(){this.$inertia.reload({method:"get",data:{search:this.searchInput},preserveState:!0,replace:!0})},300)}},q={class:"flex flex-col h-screen"},P={class:"flex-grow"},T={class:"relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10"},K={class:"w-full min-w-0 mt-4 px-1 md:px-6"},L={class:"relative overflow-hidden"},R={class:"max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:pb-12 sm:py-5"},E={class:"text-center"},G={class:"mt-7 sm:mt-12 mx-auto max-w-xl relative space-y-4"},H={class:"relative z-10 space-x-3 p-3 bg-white border rounded-lg shadow-lg shadow-gray-100"},U={class:"flex justify-between"},$={class:"flex w-full"},A={class:""},J={class:"grid grid-cols-2 gap-3"},O={type:"button",class:"flex w-full py-2 px-4 items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"},Q={class:"mx-auto max-w-2xl hs-accordion-group grid grid-cols-1 lg:grid-cols-2 gap-3"},W={class:"hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex justify-between items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:hs-accordion-active:text-blue-500 dark:text-gray-200 dark:hover:text-gray-400 dark:focus:outline-none dark:focus:text-gray-400","aria-controls":"hs-basic-active-bordered-collapse-one"},X={id:"hs-basic-active-bordered-collapse-one",class:"hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300","aria-labelledby":"hs-active-bordered-heading-one"},Y={class:"pb-4 px-5 grid gap-3 grid-cols-1"},Z=["href"];function ee(u,t,x,te,l,c){const b=i("MainPageNavBar"),v=i("BaseIcon"),m=i("ClientFooterDown");return a(),o(r,null,[n(b,{class:"border-b",sections:u.$page.props.navigation},null,8,["sections"]),e("div",q,[e("main",P,[e("div",T,[e("article",K,[e("div",L,[e("div",R,[e("div",E,[t[6]||(t[6]=e("h1",{class:"text-2xl sm:text-4xl font-bold text-gray-800 dark:text-gray-200"}," Расписание занятий ",-1)),t[7]||(t[7]=e("div",{class:"text-center"},[e("p",{class:"mt-3 text-gray-600 dark:text-gray-400"}," Просто введите название группы ")],-1)),e("div",G,[e("form",null,[e("div",H,[e("div",U,[e("div",$,[t[3]||(t[3]=e("label",{for:"hs-search-article-1",class:"block text-sm text-gray-700 font-medium dark:text-white"},[e("span",{class:"sr-only"},"Поиск")],-1)),B(e("input",{onKeydown:t[0]||(t[0]=I(C(()=>{},["prevent"]),["enter"])),autocomplete:"off","onUpdate:modelValue":t[1]||(t[1]=s=>l.searchInput=s),onInput:t[2]||(t[2]=(...s)=>c.search&&c.search(...s)),type:"search",id:"hs-search-article-1",class:"py-2.5 px-4 block w-full border-transparent rounded-lg",placeholder:"Поиск"},null,544),[[M,l.searchInput]])])])])]),e("div",A,[e("div",J,[e("button",O,[n(v,{name:"heart",class:"shrink-0 size-4"}),t[4]||(t[4]=e("span",null,"Избранные расписания",-1))]),t[5]||(t[5]=N('<button type="button" class="flex w-full py-2 px-4 items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" data-v-a071b57e><svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-v-a071b57e><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" data-v-a071b57e></path><circle cx="9" cy="7" r="4" data-v-a071b57e></circle><line x1="19" x2="19" y1="8" y2="14" data-v-a071b57e></line><line x1="22" x2="16" y1="11" y2="11" data-v-a071b57e></line></svg><span data-v-a071b57e>Follow</span></button>',1))])])])])])]),e("div",Q,[n(F,{name:"fade"},{default:j(()=>[(a(!0),o(r,null,d(x.educationalGroups.data,s=>(a(),o("div",{key:s.id,class:"hs-accordion hs-accordion-active:border-gray-200 bg-white border-b dark:hs-accordion-active:border-gray-700 dark:bg-gray-800 dark:border-transparent",id:"hs-active-bordered-heading-one"},[e("button",W,[D(g(s.title)+" ",1),t[8]||(t[8]=e("svg",{class:"hs-accordion-active:hidden block w-3.5 h-3.5",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[e("path",{d:"M5 12h14"}),e("path",{d:"M12 5v14"})],-1)),t[9]||(t[9]=e("svg",{class:"hs-accordion-active:block hidden w-3.5 h-3.5",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[e("path",{d:"M5 12h14"})],-1))]),e("div",X,[e("div",Y,[(a(!0),o(r,null,d(s.schedules,p=>(a(),o(r,{key:p.id},[(a(!0),o(r,null,d(p.file,h=>(a(),o("a",{href:"storage/"+h.path,target:"_blank",class:"py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"},g(h.title),9,Z))),256))],64))),128))])])]))),128))]),_:1})])])])]),n(m)])],64)}const de=z(S,[["render",ee],["__scopeId","data-v-a071b57e"]]);export{de as default};