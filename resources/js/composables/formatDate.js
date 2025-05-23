// November 11, 2023
function useFormatDateFull(date) {
    if(date){
        return new Date(date).toLocaleString("en-US", {
            day: "2-digit",
            month: "long",
            year: "numeric",
        });
    }
}
// 13/11/2023, 4:00 am
function useFormatDateTime(date) {
    if(date){
        return new Date(date).toLocaleString("en-GB", {
            day: "2-digit",
            year: "numeric",
            month: "2-digit",
            hour12: true,
            hour: "numeric",
            minute: "numeric",
        });
    }
}
// 13/11/2023
function useFormatDate(date) {
    if(date && date !== '0000-00-00'){
        return new Date(date).toLocaleString("en-GB", {
            day: "2-digit",
            year: "numeric",
            month: "2-digit",
        });
    }
}

export { useFormatDateTime, useFormatDate, useFormatDateFull };
