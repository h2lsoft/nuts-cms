function nutsFaQView(ID)
{
    if(nuts_faq_lastID == ID)
    {
        $('.nuts_question').removeClass('selected');
        $('.nuts_answer').slideUp();
        nuts_faq_lastID = 0;
        return;
    }


    $('.nuts_question').removeClass('selected');
    $('.nuts_answer').hide();
    $('#nuts_question_'+ID).addClass('selected');
    $('#nuts_answer_'+ID).slideToggle('normal');
    nuts_faq_lastID = ID;
}