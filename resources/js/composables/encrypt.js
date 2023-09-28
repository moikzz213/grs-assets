import CryptoJS from "crypto-js";
const dk = "mel182";
 
function decryptData(data) {
    return data
        ? JSON.parse(CryptoJS.AES.decrypt(data, dk).toString(CryptoJS.enc.Utf8))
        : null;
}

function encryptData(data) {
    return CryptoJS.AES.encrypt(JSON.stringify(data), dk).toString();
}

export { encryptData, decryptData };
