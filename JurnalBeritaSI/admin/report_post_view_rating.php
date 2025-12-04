<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require('fpdf/fpdf.php'); 
require('../Config/database.php');

// Tangkap parameter sort_option
$sort_option = $_GET['sort_option'] ?? 'all';

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../images/Logo-Telkom.png', 20, 13, 30); // Posisi X=10
        $this->Image('../images/Logo-SI.png', $this->GetPageWidth() - 50, 8, 30); // Posisi X = lebar halaman - 40 (untuk menjaga padding kanan)

        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Telkom University Jakarta', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Jalan Minangkabau Barat No.50, RT.1/RW.1, Ps. Manggis,', 0, 1, 'C');
        $this->Cell(0, 5, 'Kecamatan Setiabudi, Kota Jakarta Selatan, DKI Jakarta 12970', 0, 1, 'C');

        $this->Ln(8);
        // Garis horizontal
        $this->SetLineWidth(0.7);
        $this->Line(24, $this->GetY(), 273, $this->GetY());
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Laporan Post View dan Rating', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->Image('../images/back-telkom.png', 0, $this->GetPageHeight() - 10, $this->GetPageWidth(), 10);
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Tabel data 
    function FancyTable($header, $data)
    {
        $x_start_table = $this->GetX();

        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 10);
        // Header
        $w = array(190, 30, 30); 
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(240, 248, 255); // Warna latar belakang baris data
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 10);
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->SetX($x_start_table);

            $x = $this->GetX();
            $y = $this->GetY();

            // Tinggi MultiCell
            $nb = $this->NbLines($w[0], $row[0]);
            $h = 6 * $nb;

            // Simpan posisi awal baris
            $x_start_row = $x;
            $y_start_row = $y;

            // Tulis judul artikel dengan MultiCell (dengan border kiri dan kanan)
            $this->MultiCell($w[0], 6, $row[0], 'LR', 'L', $fill);

            // Reset posisi untuk kolom Views
            $this->SetXY($x + $w[0], $y_start_row);

            // border kiri dan kanan
            $this->Cell($w[1], $h, $row[1], 'LR', 0, 'C', $fill);

            // Reset posisi untuk kolom Rating
            $this->SetXY($x + $w[0] + $w[1], $y_start_row);

            // Isi kolom Rating dengan tinggi yang sama (border kiri dan kanan)
            $this->Cell($w[2], $h, number_format($row[2], 2), 'LR', 0, 'C', $fill);

            // Gambar border bawah untuk seluruh baris dan pindah ke baris berikutnya
            $this->SetXY($x_start_row, $y_start_row + $h);
            $this->Cell(array_sum($w), 0, '', 'T');

            $fill = !$fill;
        }
    }

    // Fungsi menghitung jumlah baris MultiCell
    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

// Buat objek PDF baru
$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Header tabel
$header = array('Judul artikel', 'Dilihat', 'Penilaian');

// Ambil data dari database dengan filter
$sql_order_by = "ORDER BY p.views DESC"; // Default
$sql_limit = ""; // Default
$report_limit = 5; // Jumlah postingan 

switch ($sort_option) {
    case 'views_desc':
        $sql_order_by = "ORDER BY p.views DESC";
        $sql_limit = "LIMIT $report_limit";
        break;
    case 'views_asc':
        $sql_order_by = "ORDER BY p.views ASC";
        $sql_limit = "LIMIT $report_limit";
        break;
    case 'rating_desc':
        $sql_order_by = "ORDER BY average_rating DESC";
        $sql_limit = "LIMIT $report_limit";
        break;
    case 'rating_asc':
        $sql_order_by = "ORDER BY average_rating ASC";
        $sql_limit = "LIMIT $report_limit";
        break;
    case 'all':
    default:
        $sql_order_by = "ORDER BY p.views DESC";
        $sql_limit = ""; // Tanpa limit untuk semua
        break;
}

$sql = "SELECT p.title, p.views, COALESCE(AVG(r.rating), 0) as average_rating 
        FROM post p
        LEFT JOIN post_ratings r ON p.id = r.post_id
        GROUP BY p.id 
        $sql_order_by $sql_limit";
$result = $connection->query($sql);

$data = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array($row['title'], $row['views'], $row['average_rating']);
    }
}

// Hitung lebar total tabel
$tableWidth = 190 + 30 + 30; 
// Hitung posisi X untuk menengahkan tabel
$startX = ($pdf->GetPageWidth() - $tableWidth) / 2;

$pdf->SetX($startX);
$pdf->FancyTable($header, $data);

$connection->close();

$pdf->Output();
