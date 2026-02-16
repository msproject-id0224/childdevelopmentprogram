export function __ (key, replace = {}) {
    const translations = window.translations || {};
    
    let translation = translations[key] || key;

    Object.keys(replace).forEach(function (key) {
        translation = translation.replace(new RegExp(':' + key, 'g'), replace[key]);
    });

    return translation;
}
