// 13/11/2023
function randomAlphaString(length) {
    let result = '';
    const characters =
      'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

function limitText(text, length) {
  if(text.length > length) {
    return text.substring(0,length)+ "..";
  } 
  return text;
}

export { randomAlphaString, limitText };
