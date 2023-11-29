class CaseConverterClass {
    convertToUpperCase = (value) => value.toUpperCase();

    convertToLowerCase = (value) => value.toLowerCase();

    convertToStartCase = (value) => {
        let returnValue = "";
        let makeNextUppercase = true;
        value = value.toLowerCase();
        for (let i = 0; i < value.length; i++) {
            let c = value.charAt(i);
            if (c.match(/^\s+$/g) || c.match(/[\(\)\[\]\{\}\\\/]/g)) {
                makeNextUppercase = true;
            } else if (makeNextUppercase) {
                c = c.toUpperCase();
                makeNextUppercase = false;
            }
            returnValue += c;
        }
        return returnValue;
    };

    convertToAlternatingCase = (value) => {
        let returnValue = "";
        let j = 0;
        value = value.toLowerCase();
        for (let i = 0; i < value.length; i++) {
            let c = value.charAt(i);
            if (c.toUpperCase() !== c) {
                if (j % 2 === 0) {
                    c = c.toUpperCase();
                }
                j++;
            }
            returnValue += c;
        }
        return returnValue;
    };

    convertToCamelCase = (value) => {
        const replace = "/";
        value = value.replace(/[\(\)\[\]\{\}\=\?\!\.\:,\-_\+\\\"#~\/]/g, " ");
        value = this.convertToStartCase(value);
        return value.replace(/\s+/g, "");
    };

    convertToSnakeCase = (value) => {
        value = value.replace(/[\(\)\[\]\{\}\=\?\!\.\:,\-_\+\\\"#~\/]/g, " ");
        value = this.convertToStartCase(value);
        value = value.replace(/^\s+/g, "");
        value = value.replace(/\s+$/g, "");
        return value.replace(/\s+/, "_");
    };

    convertToKebabCase = (value) => {
        value = value.replace(/[\(\)\[\]\{\}\=\?\!\.\:,\-_\+\\\"#~\/]/g, " ");
        value = value.toLowerCase();
        value = value.replace(/^\s+/g, "");
        value = value.replace(/\s+$/g, "");
        return value.replace(/\s+/, "-");
    };

    convertToStudlyCaps = (value) => {
        let returnValue = "";
        let numOfFollowingUppercase = 0;
        let numOfFollowingLowercase = 0;
        let doCapitalLetter = false;
        value = value.toLowerCase();
        for (let i = 0; i < value.length; i++) {
            let c = value.charAt(i);
            if (c.toUpperCase() !== c.toLowerCase()) {
                if (numOfFollowingUppercase < 2) {
                    if (Math.floor(Math.random() * 100 + 1) % 2 === 0) {
                        doCapitalLetter = true;
                        numOfFollowingUppercase++;
                    } else {
                        doCapitalLetter = false;
                        numOfFollowingUppercase = 0;
                    }
                } else {
                    doCapitalLetter = false;
                    numOfFollowingUppercase = 0;
                }
                if (!doCapitalLetter) {
                    numOfFollowingLowercase++;
                }
                if (numOfFollowingLowercase > 3) {
                    doCapitalLetter = true;
                    numOfFollowingLowercase = 0;
                    numOfFollowingUppercase++;
                }
                if (doCapitalLetter) {
                    c = c.toUpperCase();
                }
            }
            returnValue += c;
        }
        return returnValue;
    };

    invertCase = (value) => {
        let returnValue = "";
        for (let i = 0; i < value.length; i++) {
            let c = value.charAt(i);
            if (c !== c.toLowerCase()) {
                c = c.toLowerCase();
            } else if (c !== c.toUpperCase()) {
                c = c.toUpperCase();
            }
            returnValue += c;
        }
        return returnValue;
    };
}

export const CaseConverter = new CaseConverterClass();