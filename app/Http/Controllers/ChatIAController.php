namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Producto;

class ChatIAController extends Controller
{
    public function preguntar(Request $request)
    {
        $pregunta = $request->input('pregunta');

        // Datos del sistema
        $productosVencer = Producto::whereDate('fecha_vencimiento', '<=', now()->addDays(30))->get(['nombre', 'fecha_vencimiento']);
        $productosStockBajo = Producto::whereColumn('stock', '<=', 'stock_minimo')->get(['nombre', 'stock']);
        $productosTotales = Producto::count();

        $contexto = [
            'productos_por_vencer' => $productosVencer,
            'productos_stock_bajo' => $productosStockBajo,
            'total_productos' => $productosTotales,
        ];

        $apiKey = env('OPENAI_API_KEY'); // Aquí tomas la clave del .env

        $respuesta = Http::withHeaders([
            'Authorization' => "Bearer $apiKey"
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un asistente IA para inventario. Usa los datos proporcionados para ayudar.'],
                ['role' => 'user', 'content' => "Datos del sistema:\n" . json_encode($contexto) . "\n\nPregunta: " . $pregunta]
            ],
            'temperature' => 0.7
        ]);

        return response()->json([
            'respuesta' => $respuesta['choices'][0]['message']['content']
        ]);
    }
}
