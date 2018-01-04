function setLike(alias, content) {
    $.ajax({
        url: LIKE_URL,
        type: 'POST',
        data: {
            alias: alias,
            content: content,
            ajax: true
        },
        success: function(res) {
            if(res)
            {
                if (res == 'no login')
                {
                    alert(LIKE_ERROR_USER_NOT_LOGIN);
                }
                else
                {
                    if(res.setLike)
                        pageLikesFavicon.style.color = 'red';
                    else
                        pageLikesFavicon.style.color = 'gray';
                    pageLikesCount.innerText = res.count;
                }
            }
            else
                alert('Error user like!');
        }
    })
}