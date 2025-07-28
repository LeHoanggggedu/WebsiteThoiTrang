window.addEventListener("load", function() {
    const marquee1 = document.querySelector(".mq1");
    const marquee2 = document.querySelector(".mq2");

    if (marquee1 && marquee2) {
        function initializeMarquee(marquee, direction) {
            const marqueeContent = marquee.querySelector('div');
            if (!marqueeContent) return;

            const marqueeItems = Array.from(marqueeContent.children);
            let cloneCount = 0;

            // Sao chép nội dung liên tục để đảm bảo hiệu ứng cuộn không ngừng
            setInterval(() => {
                marqueeItems.forEach(item => {
                    const clone = item.cloneNode(true);
                    marqueeContent.appendChild(clone);
                    cloneCount++;
                });
                console.log(`Marquee initialized ${cloneCount} times`);
            }, 4000);
        }

        initializeMarquee(marquee1, "right");
        initializeMarquee(marquee2, "left");
    } else {
        console.error("Marquee elements not found.");
    }
});
