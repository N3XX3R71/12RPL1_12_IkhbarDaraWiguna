document.addEventListener("DOMContentLoaded", function () {
  // ========= Rating Bintang =========
  const ratingContainer = document.getElementById("ratingContainer");
  const stars = ratingContainer ? ratingContainer.querySelectorAll("i") : [];
  let currentRating = 0; 
  let userStoredRating = 0; 
  let averageGlobalRating = 0; 

  const postId = ratingContainer ? ratingContainer.dataset.postId : null;
  const userId = ratingContainer ? ratingContainer.dataset.userId : null;
  const initialUserRating = ratingContainer
    ? parseInt(ratingContainer.dataset.initialRating)
    : 0;
  const initialAverageRating = ratingContainer
    ? parseFloat(ratingContainer.dataset.averageRating)
    : 0;

  const averageRatingDisplay = document.getElementById("averageRatingDisplay");

  // Fungsi untuk memperbarui tampilan bintang berdasarkan rating rata-rata
  function updateStarsDisplay(ratingToDisplay) {
    stars.forEach((star, index) => {
      if (index < ratingToDisplay) {
        star.classList.replace("bx-star", "bxs-star");
      } else {
        star.classList.replace("bxs-star", "bx-star");
      }
    });
    if (averageRatingDisplay) {
      averageRatingDisplay.textContent = `Rata-rata: ${ratingToDisplay.toFixed(
        1
      )}`;
    }
  }

  // Fungsi untuk menandai rating pengguna yang sudah ada
  function markUserRating(rating) {
    stars.forEach((star, index) => {
      if (index < rating) {
        star.classList.add("rated");
      } else {
        star.classList.remove("rated");
      }
    });
  }

  // Inisialisasi rating saat halaman dimuat
  if (ratingContainer) {
    userStoredRating = initialUserRating;
    averageGlobalRating = initialAverageRating;

    updateStarsDisplay(averageGlobalRating); // Tampilkan rata-rata global saat inisialisasi
    markUserRating(userStoredRating); // Tandai rating pengguna jika ada

    if (!userId) {
      // Jika pengguna belum login, nonaktifkan interaksi rating
      stars.forEach((star) => {
        star.style.cursor = "default";
      });
      if (averageRatingDisplay) {
        averageRatingDisplay.textContent = `Rata-rata: ${averageGlobalRating.toFixed(
          1
        )} (Login untuk memberi rating)`;
      }
    } else {
      // Tambahkan event listener untuk setiap bintang
      stars.forEach((star) => {
        star.addEventListener("click", async function () {
          const ratingValue = parseInt(this.dataset.rating);

          try {
            const formData = new FormData();
            formData.append("post_id", postId);
            formData.append("user_id", userId);
            formData.append("rating", ratingValue);

            const response = await fetch("rating-logic.php", {
              method: "POST",
              body: formData,
            });

            const data = await response.json();

            if (data.success) {
              averageGlobalRating = data.average_rating;
              userStoredRating = data.user_rating;
              updateStarsDisplay(averageGlobalRating);
              markUserRating(userStoredRating);
              alert(
                `Rating Anda (${userStoredRating} bintang) berhasil disimpan! Rata-rata: ${averageGlobalRating}`
              );
            } else {
              alert("Gagal memberi rating: " + data.message);
            }
          } catch (error) {
            console.error("Error submitting rating:", error);
            alert("Terjadi kesalahan saat memberi rating.");
          }
        });

        star.addEventListener("mouseover", function () {
          updateStarsDisplay(parseInt(this.dataset.rating));
        });

        star.addEventListener("mouseout", function () {
          updateStarsDisplay(averageGlobalRating); // Kembali ke rata-rata global
          markUserRating(userStoredRating); // Pertahankan tanda rating pengguna
        });
      });
    }
  }

  // Tombol Share
  const shareBtn = document.getElementById("shareBtn");

  if (shareBtn) {
    shareBtn.addEventListener("click", function () {
      if (navigator.share) {
        navigator
          .share({
            title: "Cek berita ini!",
            url: window.location.href,
          })
          .then(() => {
            alert("Berita berhasil dibagikan!");
          })
          .catch((error) => {
            console.log("Gagal berbagi:", error);
          });
      } else {
        alert("Fitur berbagi tidak didukung di browser ini.");
      }
    });
  }

  // ========= Komentar: Reply toggle + Kebab menu (titik tiga) =========
  // Toggle form balasan ketika tombol "Balas" diklik
  const replyButtons = document.querySelectorAll(".reply-btn");
  replyButtons.forEach(function (button) {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      const commentList = button.closest(".comment-list");
      if (!commentList) return;
      const replyForm = commentList.querySelector(".reply-form");
      if (replyForm) {
        replyForm.classList.toggle("is-hidden");
      }
    });
  });

  // Kebab menu: buka/tutup menu Edit/Hapus
  function closeAllMenus(exceptInput) {
    document
      .querySelectorAll(".comment-actions .menu-toggle")
      .forEach(function (input) {
        if (input !== exceptInput) input.checked = false;
      });
  }

  const actionBlocks = document.querySelectorAll(".comment-actions");
  actionBlocks.forEach(function (actions) {
    const input = actions.querySelector(".menu-toggle");
    const kebabBtn = actions.querySelector(".kebab-btn");
    const dropdown = actions.querySelector(".menu-dropdown");

    if (kebabBtn && input) {
      kebabBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        input.checked = !input.checked;
        closeAllMenus(input);
      });
      input.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    if (dropdown) {
      dropdown.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }
  });

  // Tutup menu saat klik di luar
  document.addEventListener("click", function () {
    closeAllMenus();
  });

  // Tutup menu saat tekan Escape
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeAllMenus();
    }
  });

  // ========= Slider Berita Otomatis =========
  const sliderItems = document.querySelectorAll(".slider-item");
  console.log("Jumlah slider items ditemukan:", sliderItems.length);
  let currentSlide = 0;
  let slideInterval;

  function showSlide(index) {
    console.log("Menampilkan slide:", index);
    sliderItems.forEach((item, i) => {
      item.classList.remove("active");
      if (i === index) {
        item.classList.add("active");
      }
    });
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % sliderItems.length;
    console.log("Slide berikutnya, indeks:", currentSlide);
    showSlide(currentSlide);
  }

  function startSlider() {
    slideInterval = setInterval(nextSlide, 5000);
  }

  function stopSlider() {
    clearInterval(slideInterval);
  }

  if (sliderItems.length > 0) {
    showSlide(currentSlide);
    startSlider();

    const sliderSection = document.querySelector(".slider");
    if (sliderSection) {
      sliderSection.addEventListener("mouseenter", stopSlider);
      sliderSection.addEventListener("mouseleave", startSlider);
    }
  }
});

const navItems = document.querySelector(".nav_items");
const openNavBtn = document.querySelector("#open_nav-btn");
const closeNavBtn = document.querySelector("#close_nav-btn");

// opens nav dropdown
const openNav = () => {
  navItems.style.display = "flex";
  openNavBtn.style.display = "none";
  closeNavBtn.style.display = "inline-block";
};

// close nav dropdown
const closeNav = () => {
  navItems.style.display = "none";
  openNavBtn.style.display = "inline-block";
  closeNavBtn.style.display = "none";
};

openNavBtn.addEventListener("click", openNav);
closeNavBtn.addEventListener("click", closeNav);

// Toggle nav profile dropdown on click
const navProfile = document.querySelector(".nav_profile");
if (navProfile) {
  navProfile.addEventListener("click", function (event) {
    event.stopPropagation(); // Mencegah event click menyebar ke document
    this.classList.toggle("show-dropdown");
  });
}

// Close nav profile dropdown when clicking outside
document.addEventListener("click", function (event) {
  if (
    navProfile &&
    navProfile.classList.contains("show-dropdown") &&
    !navProfile.contains(event.target)
  ) {
    navProfile.classList.remove("show-dropdown");
  }
});
