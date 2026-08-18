(() => {
    'use strict';
    const forms=document.querySelectorAll('[data-password-form]');
    forms.forEach((form)=>{
        const password=form.querySelector('[data-password]');
        const confirmation=form.querySelector('[data-password-confirmation]');
        const overall=form.querySelector('[data-password-overall]');
        const rules=[...form.querySelectorAll('[data-password-rule]')];
        const tests={length:(v,el)=>v.length>=Number(el.dataset.minLength||8),uppercase:(v)=>/[A-Z]/.test(v),lowercase:(v)=>/[a-z]/.test(v),number:(v)=>/\d/.test(v)};
        const setState=(el,state)=>{el.classList.remove('text-success','text-danger','text-secondary');el.classList.add(state==='pass'?'text-success':state==='fail'?'text-danger':'text-secondary');const icon=el.querySelector('[data-state-icon]');if(icon)icon.textContent=state==='pass'?'✓':state==='fail'?'✕':'○';};
        const update=()=>{if(!password)return;const value=password.value;let all=value!=='';rules.forEach((el)=>{const pass=tests[el.dataset.passwordRule]?.(value,el)??false;all=all&&pass;setState(el,value===''?'neutral':pass?'pass':'fail');});if(overall){overall.textContent=value===''?'Password requirements have not been evaluated yet.':all?'All password requirements are satisfied.':'Password requirements are not yet satisfied.';overall.className=all?'text-success small':'text-secondary small';}if(confirmation){const feedback=form.querySelector('[data-confirmation-feedback]');const empty=confirmation.value==='';const match=!empty&&confirmation.value===value;confirmation.setCustomValidity(empty||match?'':'Passwords do not match.');if(feedback){feedback.textContent=empty?'Enter the password again.':match?'✓ Passwords match.':'✕ Passwords do not match.';feedback.className=empty?'text-secondary small':match?'text-success small':'text-danger small';}}};
        password?.addEventListener('input',update);confirmation?.addEventListener('input',update);update();
    });
    document.querySelectorAll('[data-password-toggle]').forEach((button)=>button.addEventListener('click',()=>{const input=document.getElementById(button.dataset.passwordToggle);if(!input)return;const show=input.type==='password';input.type=show?'text':'password';button.setAttribute('aria-pressed',show?'true':'false');button.textContent=show?'Hide password':'Show password';button.setAttribute('aria-label',button.textContent);}));
})();
